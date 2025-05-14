<?php

namespace App\Plugins\OTA\Services\Parsers;

use SimpleXMLElement;
use DOMDocument;
use Illuminate\Support\Str;

class XmlDataParser extends AbstractDataParser
{
    /**
     * Format type this parser handles
     * 
     * @var string
     */
    protected string $formatType = 'xml';
    
    /**
     * Path separator used by this parser
     * 
     * @var string
     */
    protected string $pathSeparator = '.';

    /**
     * Parse XML string and extract all paths
     *
     * @param string $content
     * @return array
     */
    public function parse(string $content): array
    {
        $content = $this->sanitizeXml($content);
        
        try {
            $xml = new SimpleXMLElement($content);
            $paths = [];
            $this->extractPaths($xml, '', $paths);
            
            return $paths;
        } catch (\Exception $e) {
            return ['error' => 'XML Parse error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Sanitize XML content
     *
     * @param string $content
     * @return string
     */
    protected function sanitizeXml(string $content): string
    {
        // Clean whitespace and line breaks
        $content = trim($content);
        
        // Check XML declaration
        if (!Str::startsWith($content, '<?xml')) {
            $content = '<?xml version="1.0" encoding="UTF-8"?>' . $content;
        }
        
        // Handle characters properly
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        
        // Temporarily disable error reporting
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadXML($content);
        libxml_use_internal_errors($internalErrors);
        
        return $dom->saveXML();
    }
    
    /**
     * Extract all paths from XML recursively
     *
     * @param SimpleXMLElement $xml
     * @param string $path
     * @param array &$paths
     * @return void
     */
    protected function extractPaths(SimpleXMLElement $xml, string $path, array &$paths): void
    {
        $currentPath = $path ? $path : $xml->getName();
        
        // If the current element is not empty and has no children, get its value
        if ((string)$xml && !$xml->children()->count()) {
            $paths[$currentPath] = [
                'type' => 'element',
                'value' => (string)$xml,
                'attributes' => $this->getAttributes($xml)
            ];
        } else {
            // Get attributes
            $attributes = $this->getAttributes($xml);
            if (!empty($attributes)) {
                $paths[$currentPath] = [
                    'type' => 'element',
                    'value' => null,
                    'attributes' => $attributes
                ];
            }
        }
        
        // Process child elements recursively
        foreach ($xml->children() as $childName => $child) {
            $childPath = $path ? "$path.$childName" : $childName;
            $this->extractPaths($child, $childPath, $paths);
        }
    }
    
    /**
     * Get all attributes from XML element
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function getAttributes(SimpleXMLElement $xml): array
    {
        $attributes = [];
        foreach ($xml->attributes() as $attributeName => $attributeValue) {
            $attributes[$attributeName] = (string)$attributeValue;
        }
        return $attributes;
    }
    
    /**
     * Check if the parser can handle this content
     *
     * @param string $content
     * @return bool
     */
    public function canParse(string $content): bool
    {
        $content = trim($content);
        
        // Check for XML declaration or typical XML structure
        if (Str::startsWith($content, '<?xml')) {
            return true;
        }
        
        // Check for typical XML starting with a tag
        if (preg_match('/^<[^>]+>/', $content)) {
            try {
                // Try to parse a small sample
                $xml = new SimpleXMLElement($content);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Transform data using provided mapping
     *
     * @param string $content Source content to transform
     * @param array $mapping Mapping rules
     * @param string|null $template Optional template for export transformations
     * @return mixed Transformed data
     */
    public function transform(string $content, array $mapping, ?string $template = null): mixed
    {
        // Parse the XML content
        $parsedData = $this->parse($content);
        
        if (isset($parsedData['error'])) {
            return ['error' => $parsedData['error']];
        }
        
        // Transform data based on mapping
        $transformedData = [];
        
        foreach ($mapping as $sourcePath => $targetConfig) {
            // Skip empty mappings
            if (empty($targetConfig)) {
                continue;
            }
            
            // Handle simple string target (direct field mapping)
            if (is_string($targetConfig)) {
                $value = $this->getValueFromPath($parsedData, $sourcePath);
                if ($value !== null) {
                    $this->setValueByPath($transformedData, $targetConfig, $value);
                }
                continue;
            }
            
            // Handle complex mapping with transformations
            if (is_array($targetConfig) && isset($targetConfig['target'])) {
                $value = $this->getValueFromPath($parsedData, $sourcePath);
                
                // Apply transformations if specified
                if (isset($targetConfig['transformations']) && is_array($targetConfig['transformations'])) {
                    $value = $this->applyTransformationRules($value, $targetConfig['transformations']);
                }
                
                if ($value !== null) {
                    $this->setValueByPath($transformedData, $targetConfig['target'], $value);
                }
            }
        }
        
        // If a template is provided for export, use it
        if ($template && !empty($transformedData)) {
            return $this->renderTemplate($template, $transformedData);
        }
        
        return $transformedData;
    }
    
    /**
     * Get a value from the parsed XML data using the source path
     *
     * @param array $parsedData
     * @param string $sourcePath
     * @return mixed
     */
    protected function getValueFromPath(array $parsedData, string $sourcePath): mixed
    {
        // Handle attribute notation
        if (preg_match('/^(.+)\[@([^\]]+)\]$/', $sourcePath, $matches)) {
            $elementPath = $matches[1];
            $attributeName = $matches[2];
            
            if (isset($parsedData[$elementPath]['attributes'][$attributeName])) {
                return $parsedData[$elementPath]['attributes'][$attributeName];
            }
            
            return null;
        }
        
        // Handle regular element
        if (isset($parsedData[$sourcePath]['value'])) {
            return $parsedData[$sourcePath]['value'];
        }
        
        return null;
    }
}