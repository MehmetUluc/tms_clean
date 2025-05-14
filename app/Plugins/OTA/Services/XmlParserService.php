<?php

namespace App\Plugins\OTA\Services;

use SimpleXMLElement;
use DOMDocument;
use Illuminate\Support\Str;

class XmlParserService
{
    /**
     * Parse JSON string and extract all paths
     *
     * @param string $jsonContent
     * @return array
     */
    public function parseJson(string $jsonContent): array
    {
        try {
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'JSON Parse hatası: ' . json_last_error_msg()];
            }

            $paths = [];
            $this->extractJsonPaths($data, '', $paths);

            return $paths;
        } catch (\Exception $e) {
            return ['error' => 'JSON Parse hatası: ' . $e->getMessage()];
        }
    }

    /**
     * Extract paths from JSON data recursively
     *
     * @param array $data
     * @param string $currentPath
     * @param array &$paths
     * @return void
     */
    private function extractJsonPaths($data, string $currentPath, array &$paths): void
    {
        if (!is_array($data)) {
            // Leaf node (value)
            $paths[$currentPath] = [
                'type' => 'value',
                'example' => (string)$data
            ];
            return;
        }

        if (empty($data)) {
            return;
        }

        // Check if this is a sequential array or associative
        $isSequential = array_keys($data) === range(0, count($data) - 1);

        if ($isSequential) {
            // For arrays, we only process the first item as example
            $this->extractJsonPaths($data[0], $currentPath, $paths);

            // Mark that this is an array
            $paths[$currentPath] = [
                'type' => 'array',
                'example' => 'Array[' . count($data) . ']'
            ];
        } else {
            // For objects, process all properties
            foreach ($data as $key => $value) {
                $newPath = $currentPath ? $currentPath . '.' . $key : $key;

                if (is_array($value)) {
                    // Recursive call for nested objects/arrays
                    $this->extractJsonPaths($value, $newPath, $paths);
                } else {
                    // Leaf node (property value)
                    $paths[$newPath] = [
                        'type' => 'value',
                        'example' => (string)$value
                    ];
                }
            }

            if ($currentPath) {
                $paths[$currentPath] = [
                    'type' => 'object',
                    'example' => 'Object'
                ];
            }
        }
    }

    /**
     * Get example data from JSON content
     *
     * @param string $jsonContent
     * @return array
     */
    public function getJsonExampleData(string $jsonContent): array
    {
        $paths = $this->parseJson($jsonContent);

        if (isset($paths['error'])) {
            return $paths;
        }

        $result = [];

        foreach ($paths as $path => $data) {
            if (empty($path)) continue;

            $result[] = [
                'path' => $path,
                'type' => $data['type'],
                'example' => $data['example'] ?? null
            ];
        }

        // Sort by path
        usort($result, function ($a, $b) {
            return strcmp($a['path'], $b['path']);
        });

        return $result;
    }
    /**
     * Parse XML string and extract all paths
     *
     * @param string $xmlContent
     * @return array
     */
    public function parseXml(string $xmlContent): array
    {
        $xmlContent = $this->sanitizeXml($xmlContent);
        
        try {
            $xml = new SimpleXMLElement($xmlContent);
            $paths = [];
            $this->extractPaths($xml, '', $paths);
            
            return $paths;
        } catch (\Exception $e) {
            return ['error' => 'XML Parse hatası: ' . $e->getMessage()];
        }
    }
    
    /**
     * Sanitize XML content
     *
     * @param string $xmlContent
     * @return string
     */
    protected function sanitizeXml(string $xmlContent): string
    {
        // XML içeriğindeki boşlukları ve yeni satırları temizle
        $xmlContent = trim($xmlContent);
        
        // XML bildirimini kontrol et
        if (!Str::startsWith($xmlContent, '<?xml')) {
            $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . $xmlContent;
        }
        
        // Karakterleri doğru şekilde işle
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        
        // Hata raporlamasını geçici olarak kapat
        $internalErrors = libxml_use_internal_errors(true);
        $dom->loadXML($xmlContent);
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
        
        // Mevcut elementin içi boş değilse ve alt elemanı yoksa değerini al
        if ((string)$xml && !$xml->children()->count()) {
            $paths[$currentPath] = [
                'type' => 'element',
                'value' => (string)$xml,
                'attributes' => $this->getAttributes($xml)
            ];
        } else {
            // Öznitelikleri (attributes) al
            $attributes = $this->getAttributes($xml);
            if (!empty($attributes)) {
                $paths[$currentPath] = [
                    'type' => 'element',
                    'value' => null,
                    'attributes' => $attributes
                ];
            }
        }
        
        // Alt elemanlar için recursive işlem
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
     * Format parsed XML paths for frontend display
     *
     * @param array $paths
     * @return array
     */
    public function formatPathsForUI(array $paths): array
    {
        $formattedPaths = [];
        
        foreach ($paths as $path => $data) {
            $item = [
                'path' => $path,
                'type' => $data['type'],
                'example' => $data['value'] ?? '',
                'attributes' => []
            ];
            
            // Her öznitelik için ayrı bir giriş oluştur
            if (!empty($data['attributes'])) {
                foreach ($data['attributes'] as $attrName => $attrValue) {
                    $attributePath = $path . "[@" . $attrName . "]";
                    $formattedPaths[] = [
                        'path' => $attributePath,
                        'type' => 'attribute',
                        'example' => $attrValue,
                        'parent' => $path
                    ];
                    
                    $item['attributes'][] = [
                        'name' => $attrName,
                        'value' => $attrValue,
                        'path' => $attributePath
                    ];
                }
            }
            
            $formattedPaths[] = $item;
        }
        
        // Pathleri alfabetik olarak sırala
        usort($formattedPaths, function($a, $b) {
            return strcmp($a['path'], $b['path']);
        });
        
        return $formattedPaths;
    }
    
    /**
     * Find example data from XML
     *
     * @param string $xmlContent
     * @return array
     */
    public function getExampleData(string $xmlContent): array
    {
        $paths = $this->parseXml($xmlContent);
        return $this->formatPathsForUI($paths);
    }
    
    /**
     * Generate mapping template from parsed paths
     *
     * @param array $paths
     * @return array
     */
    public function generateMappingTemplate(array $paths): array
    {
        $mapping = [];
        
        foreach ($paths as $item) {
            if ($item['type'] === 'element') {
                $mapping[$item['path']] = '';
            } else if ($item['type'] === 'attribute') {
                $mapping[$item['path']] = '';
            }
        }
        
        return $mapping;
    }
}