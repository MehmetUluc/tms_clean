<?php

namespace App\Plugins\OTA\Services\Parsers;

use App\Plugins\OTA\Contracts\IDataParser;

abstract class AbstractDataParser implements IDataParser
{
    /**
     * Path separator used by this parser
     * 
     * @var string
     */
    protected string $pathSeparator = '.';
    
    /**
     * Format type this parser handles
     * 
     * @var string
     */
    protected string $formatType = '';
    
    /**
     * Get the format type this parser handles
     *
     * @return string
     */
    public function getFormatType(): string
    {
        return $this->formatType;
    }
    
    /**
     * Format parsed paths for UI display
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
            
            // Handle attributes if available
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
        
        // Sort paths alphabetically
        usort($formattedPaths, function($a, $b) {
            return strcmp($a['path'], $b['path']);
        });
        
        return $formattedPaths;
    }
    
    /**
     * Find example data from content
     *
     * @param string $content
     * @return array
     */
    public function getExampleData(string $content): array
    {
        $paths = $this->parse($content);
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
            if (isset($item['path'])) {
                $mapping[$item['path']] = '';
            }
        }
        
        return $mapping;
    }
    
    /**
     * Apply transformation rules to a value
     *
     * @param mixed $value The value to transform
     * @param array $rules Transformation rules
     * @return mixed The transformed value
     */
    protected function applyTransformationRules($value, array $rules = []): mixed
    {
        if (empty($rules) || empty($value)) {
            return $value;
        }
        
        $result = $value;
        
        foreach ($rules as $rule => $params) {
            switch ($rule) {
                case 'date_format':
                    $result = $this->transformDate($result, $params['from'] ?? '', $params['to'] ?? '');
                    break;
                case 'number_format':
                    $result = $this->transformNumber($result, $params);
                    break;
                case 'boolean':
                    $result = $this->transformBoolean($result, $params);
                    break;
                case 'trim':
                    $result = trim($result);
                    break;
                case 'uppercase':
                    $result = strtoupper($result);
                    break;
                case 'lowercase':
                    $result = strtolower($result);
                    break;
                // Add more transformation types as needed
            }
        }
        
        return $result;
    }
    
    /**
     * Transform a date value from one format to another
     *
     * @param string $value
     * @param string $fromFormat
     * @param string $toFormat
     * @return string
     */
    protected function transformDate(string $value, string $fromFormat, string $toFormat): string
    {
        if (empty($fromFormat) || empty($toFormat)) {
            return $value;
        }
        
        try {
            $date = \DateTime::createFromFormat($fromFormat, $value);
            if ($date) {
                return $date->format($toFormat);
            }
        } catch (\Exception $e) {
            // Return original if transformation fails
        }
        
        return $value;
    }
    
    /**
     * Transform a number according to specified format
     *
     * @param string $value
     * @param array $params
     * @return string
     */
    protected function transformNumber(string $value, array $params): string
    {
        $decimals = $params['decimals'] ?? 2;
        $decPoint = $params['dec_point'] ?? '.';
        $thousandsSep = $params['thousands_sep'] ?? ',';
        
        // Convert to float first to ensure it's a number
        $number = floatval($value);
        
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }
    
    /**
     * Transform a boolean value according to specified mapping
     *
     * @param string $value
     * @param array $params
     * @return string|bool
     */
    protected function transformBoolean(string $value, array $params): string|bool
    {
        $trueValues = $params['true_values'] ?? ['yes', 'y', 'true', '1'];
        $falseValues = $params['false_values'] ?? ['no', 'n', 'false', '0'];
        $outputFormat = $params['output'] ?? 'boolean';
        
        $normalizedValue = strtolower(trim($value));
        
        $isTrue = in_array($normalizedValue, $trueValues, true);
        $isFalse = in_array($normalizedValue, $falseValues, true);
        
        if (!$isTrue && !$isFalse) {
            return $value; // Return original if no match
        }
        
        if ($outputFormat === 'boolean') {
            return $isTrue;
        }
        
        if ($outputFormat === 'numeric') {
            return $isTrue ? '1' : '0';
        }
        
        if ($outputFormat === 'text') {
            return $isTrue ? 'true' : 'false';
        }
        
        if ($outputFormat === 'yes_no') {
            return $isTrue ? 'yes' : 'no';
        }
        
        return $isTrue; // Default to boolean
    }
    
    /**
     * Find a value in data using a dot-notation path
     * 
     * @param array $data
     * @param string $path
     * @return mixed
     */
    protected function getValueByPath(array $data, string $path)
    {
        $keys = explode($this->pathSeparator, $path);
        $value = $data;
        
        foreach ($keys as $key) {
            // Handle array index notation like items[0]
            if (preg_match('/^([^\[]+)\[(\d+)\]$/', $key, $matches)) {
                $arrayKey = $matches[1];
                $index = (int)$matches[2];
                
                if (!isset($value[$arrayKey]) || !isset($value[$arrayKey][$index])) {
                    return null;
                }
                
                $value = $value[$arrayKey][$index];
                continue;
            }
            
            // Handle attribute notation like [@name]
            if (preg_match('/^\[@([^\]]+)\]$/', $key, $matches)) {
                $attrName = $matches[1];
                
                if (!isset($value['attributes']) || !isset($value['attributes'][$attrName])) {
                    return null;
                }
                
                $value = $value['attributes'][$attrName];
                continue;
            }
            
            if (!isset($value[$key])) {
                return null;
            }
            
            $value = $value[$key];
        }
        
        return $value;
    }
    
    /**
     * Set a value in a nested array using dot notation
     * 
     * @param array &$array
     * @param string $path
     * @param mixed $value
     * @return void
     */
    protected function setValueByPath(array &$array, string $path, $value): void
    {
        $keys = explode($this->pathSeparator, $path);
        $current = &$array;
        
        foreach ($keys as $i => $key) {
            // Last element - set the value
            if ($i === count($keys) - 1) {
                $current[$key] = $value;
                break;
            }
            
            // Create nested array if it doesn't exist
            if (!isset($current[$key]) || !is_array($current[$key])) {
                $current[$key] = [];
            }
            
            $current = &$current[$key];
        }
    }
    
    /**
     * Basic template rendering for simple variable substitution
     * 
     * @param string $template
     * @param array $data
     * @return string
     */
    protected function renderTemplate(string $template, array $data): string
    {
        $result = $template;
        
        // Simple variable substitution using {{var}} syntax
        preg_match_all('/\{\{([^}]+)\}\}/', $template, $matches);
        
        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $i => $path) {
                $path = trim($path);
                $value = $this->getValueByPath($data, $path) ?? '';
                $result = str_replace($matches[0][$i], $value, $result);
            }
        }
        
        return $result;
    }
}