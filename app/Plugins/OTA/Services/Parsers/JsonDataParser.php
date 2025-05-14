<?php

namespace App\Plugins\OTA\Services\Parsers;

use Illuminate\Support\Str;

class JsonDataParser extends AbstractDataParser
{
    /**
     * Format type this parser handles
     * 
     * @var string
     */
    protected string $formatType = 'json';
    
    /**
     * Path separator used by this parser
     * 
     * @var string
     */
    protected string $pathSeparator = '.';

    /**
     * Parse JSON string and extract all paths
     *
     * @param string $content
     * @return array
     */
    public function parse(string $content): array
    {
        try {
            $json = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'JSON Parse error: ' . json_last_error_msg()];
            }
            
            $paths = [];
            $this->extractPaths($json, '', $paths);
            
            return $paths;
        } catch (\Exception $e) {
            return ['error' => 'JSON Parse error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Extract all paths from JSON recursively
     *
     * @param mixed $data
     * @param string $path
     * @param array &$paths
     * @return void
     */
    protected function extractPaths($data, string $path, array &$paths): void
    {
        // Handle scalar values (final nodes)
        if (is_scalar($data) || $data === null) {
            $paths[$path] = [
                'type' => 'element',
                'value' => is_bool($data) ? ($data ? 'true' : 'false') : (string)$data,
                'attributes' => []
            ];
            return;
        }
        
        // Handle arrays (indexed or associative)
        if (is_array($data)) {
            // First, add the current path as a container
            if (!empty($path)) {
                $paths[$path] = [
                    'type' => 'container',
                    'value' => null,
                    'attributes' => []
                ];
            }
            
            // Process each item in the array
            foreach ($data as $key => $value) {
                $newPath = empty($path) ? $key : $path . $this->pathSeparator . $key;
                
                // For sequential numeric arrays, use array notation
                if (is_int($key) && array_keys($data) === range(0, count($data) - 1)) {
                    $arrayPath = $path . '[' . $key . ']';
                    $this->extractPaths($value, $arrayPath, $paths);
                } else {
                    $this->extractPaths($value, $newPath, $paths);
                }
            }
        }
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
        
        // Simple check for JSON structure (starts with { or [)
        if ($content && ($content[0] === '{' || $content[0] === '[')) {
            // Try to decode a small sample
            json_decode($content);
            return json_last_error() === JSON_ERROR_NONE;
        }
        
        return false;
    }
    
    /**
     * Format parsed paths specifically for JSON data
     *
     * @param array $paths
     * @return array
     */
    public function formatPathsForUI(array $paths): array
    {
        $formattedPaths = [];
        
        foreach ($paths as $path => $data) {
            // Skip error entries
            if ($path === 'error') {
                continue;
            }
            
            $item = [
                'path' => $path,
                'type' => $data['type'],
                'example' => $data['value'] ?? '',
            ];
            
            $formattedPaths[] = $item;
        }
        
        // Sort paths alphabetically
        usort($formattedPaths, function($a, $b) {
            return strcmp($a['path'], $b['path']);
        });
        
        return $formattedPaths;
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
        // Parse the JSON content
        $parsedData = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'JSON Parse error: ' . json_last_error_msg()];
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
                $value = $this->getValueByPath($parsedData, $sourcePath);
                if ($value !== null) {
                    $this->setValueByPath($transformedData, $targetConfig, $value);
                }
                continue;
            }
            
            // Handle complex mapping with transformations
            if (is_array($targetConfig) && isset($targetConfig['target'])) {
                $value = $this->getValueByPath($parsedData, $sourcePath);
                
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
     * Convert a complex object to a flat array with dot notation paths
     *
     * @param array $data
     * @param string $prefix
     * @return array
     */
    public function flattenData(array $data, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            $newKey = $prefix ? "$prefix.$key" : $key;
            
            if (is_array($value) && !empty($value) && !$this->isAssociativeArray($value)) {
                // Handle numeric arrays
                foreach ($value as $i => $item) {
                    $arrayKey = "$newKey[$i]";
                    if (is_array($item)) {
                        $result = array_merge($result, $this->flattenData($item, $arrayKey));
                    } else {
                        $result[$arrayKey] = $item;
                    }
                }
            } else if (is_array($value)) {
                // Process nested objects
                $result = array_merge($result, $this->flattenData($value, $newKey));
            } else {
                // Handle scalar values
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Check if an array is associative or sequential
     *
     * @param array $array
     * @return bool
     */
    protected function isAssociativeArray(array $array): bool
    {
        if (empty($array)) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }
}