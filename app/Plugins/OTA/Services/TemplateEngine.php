<?php

namespace App\Plugins\OTA\Services;

class TemplateEngine
{
    /**
     * The template content
     *
     * @var string
     */
    protected string $template;

    /**
     * Data for template rendering
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Constructor
     *
     * @param string|null $template
     */
    public function __construct(?string $template = null)
    {
        if ($template) {
            $this->setTemplate($template);
        }
    }

    /**
     * Set the template
     *
     * @param string $template
     * @return self
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Set the data for template rendering
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Render the template with provided data
     *
     * @param array|null $data Optional data to use instead of previously set data
     * @return string
     */
    public function render(?array $data = null): string
    {
        if ($data !== null) {
            $this->data = $data;
        }

        $result = $this->template;

        // Process basic variable substitution
        $result = $this->processVariables($result);
        
        // Process conditional blocks
        $result = $this->processConditionals($result);
        
        // Process each loops
        $result = $this->processEachLoops($result);
        
        // Process for loops
        $result = $this->processForLoops($result);
        
        // Process concat helpers
        $result = $this->processConcat($result);
        
        // Process format helpers
        $result = $this->processFormat($result);

        return $result;
    }

    /**
     * Process simple variable substitution
     *
     * @param string $template
     * @return string
     */
    protected function processVariables(string $template): string
    {
        return preg_replace_callback('/\{\{\s*([^}]+)\s*\}\}/', function($matches) {
            $path = trim($matches[1]);
            
            // Skip processing if it's a helper block
            if (strpos($path, '#') === 0) {
                return $matches[0];
            }
            
            $value = $this->getValueFromPath($path);
            return $value !== null ? $value : '';
        }, $template);
    }

    /**
     * Process conditional blocks in the template
     *
     * @param string $template
     * @return string
     */
    protected function processConditionals(string $template): string
    {
        $pattern = '/\{\{\s*#if\s+([^}]+)\s*\}\}(.*?)\{\{\s*\/if\s*\}\}/s';
        
        return preg_replace_callback($pattern, function($matches) {
            $condition = trim($matches[1]);
            $content = $matches[2];
            
            // Check for comparison operators
            if (strpos($condition, '==') !== false) {
                list($leftSide, $rightSide) = array_map('trim', explode('==', $condition, 2));
                
                // Handle variable on left side
                $leftValue = $this->getValueFromPath($leftSide);
                
                // Handle string literal on right side (remove quotes)
                if (preg_match('/^[\'"](.*)[\'"](.*?)$/', $rightSide, $valueMatches)) {
                    $rightValue = $valueMatches[1];
                } else {
                    $rightValue = $this->getValueFromPath($rightSide);
                }
                
                return $leftValue == $rightValue ? $this->processTemplate($content) : '';
            }
            
            // Simple variable check
            $value = $this->getValueFromPath($condition);
            return $value ? $this->processTemplate($content) : '';
        }, $template);
    }

    /**
     * Process #each loops in the template
     *
     * @param string $template
     * @return string
     */
    protected function processEachLoops(string $template): string
    {
        $pattern = '/\{\{\s*#each\s+([^}]+)\s*\}\}(.*?)\{\{\s*\/each\s*\}\}/s';
        
        return preg_replace_callback($pattern, function($matches) {
            $arrayPath = trim($matches[1]);
            $content = $matches[2];
            
            $array = $this->getValueFromPath($arrayPath);
            
            if (!is_array($array) || empty($array)) {
                return '';
            }
            
            $result = '';
            
            foreach ($array as $index => $item) {
                // Create a context with 'this' and 'index' variables
                $loopData = $this->data;
                $loopData['this'] = $item;
                $loopData['index'] = $index;
                
                // Process the content with this context
                $itemContent = $content;
                $itemContent = preg_replace_callback('/\{\{\s*([^}]+)\s*\}\}/', function($subMatches) use ($item, $index) {
                    $path = trim($subMatches[1]);
                    
                    // Replace 'this' with the current item
                    if ($path === 'this') {
                        return is_scalar($item) ? $item : json_encode($item);
                    }
                    
                    // Handle this.property syntax
                    if (strpos($path, 'this.') === 0) {
                        $itemPath = substr($path, 5);
                        if (is_array($item) && isset($item[$itemPath])) {
                            return $item[$itemPath];
                        }
                    }
                    
                    // Handle index
                    if ($path === 'index') {
                        return $index;
                    }
                    
                    // Regular variable processing
                    return $subMatches[0];
                }, $itemContent);
                
                $result .= $itemContent;
            }
            
            return $result;
        }, $template);
    }

    /**
     * Process #for loops in the template
     *
     * @param string $template
     * @return string
     */
    protected function processForLoops(string $template): string
    {
        $pattern = '/\{\{\s*#for\s+(\d+)\s+to\s+(\d+)(?:\s+step\s+(\d+))?\s*\}\}(.*?)\{\{\s*\/for\s*\}\}/s';
        
        return preg_replace_callback($pattern, function($matches) {
            $start = (int)$matches[1];
            $end = (int)$matches[2];
            $step = isset($matches[3]) ? (int)$matches[3] : 1;
            $content = $matches[4];
            
            if ($step <= 0) {
                $step = 1;
            }
            
            $result = '';
            
            for ($i = $start; $i <= $end; $i += $step) {
                // Create context with 'i' variable
                $loopData = $this->data;
                $loopData['i'] = $i;
                
                // Process the content with this context
                $itemContent = preg_replace_callback('/\{\{\s*i\s*\}\}/', function() use ($i) {
                    return $i;
                }, $content);
                
                $result .= $itemContent;
            }
            
            return $result;
        }, $template);
    }

    /**
     * Process concat helpers
     *
     * @param string $template
     * @return string
     */
    protected function processConcat(string $template): string
    {
        $pattern = '/\{\{\s*concat\s+([^}]+)\s*\}\}/';
        
        return preg_replace_callback($pattern, function($matches) {
            $parts = array_map('trim', explode(' ', $matches[1]));
            $result = '';
            
            foreach ($parts as $part) {
                // Check if it's a string literal
                if (preg_match('/^[\'"](.*)[\'"](.*?)$/', $part, $valueMatches)) {
                    $result .= $valueMatches[1];
                } else {
                    $value = $this->getValueFromPath($part);
                    $result .= $value !== null ? $value : '';
                }
            }
            
            return $result;
        }, $template);
    }

    /**
     * Process format helpers
     *
     * @param string $template
     * @return string
     */
    protected function processFormat(string $template): string
    {
        // Format date
        $datePattern = '/\{\{\s*format\s+date\s+([^\s}]+)\s+[\'"]([^\'"]+)[\'"]\s*\}\}/';
        $template = preg_replace_callback($datePattern, function($matches) {
            $path = trim($matches[1]);
            $format = $matches[2];
            
            $value = $this->getValueFromPath($path);
            
            if ($value) {
                try {
                    $date = new \DateTime($value);
                    return $date->format($format);
                } catch (\Exception $e) {
                    return $value;
                }
            }
            
            return '';
        }, $template);
        
        // Format currency
        $currencyPattern = '/\{\{\s*format\s+currency\s+([^\s}]+)(?:\s+[\'"]([^\'"]+)[\'"])?\s*\}\}/';
        $template = preg_replace_callback($currencyPattern, function($matches) {
            $path = trim($matches[1]);
            $currency = $matches[2] ?? 'TRY';
            
            $value = $this->getValueFromPath($path);
            
            if (is_numeric($value)) {
                $currencySymbols = [
                    'TRY' => '₺',
                    'USD' => '$',
                    'EUR' => '€',
                    'GBP' => '£'
                ];
                
                $symbol = $currencySymbols[$currency] ?? $currency;
                return $symbol . number_format((float)$value, 2, '.', ',');
            }
            
            return $value;
        }, $template);
        
        // Format number
        $numberPattern = '/\{\{\s*format\s+number\s+([^\s}]+)(?:\s+(\d+))?\s*\}\}/';
        $template = preg_replace_callback($numberPattern, function($matches) {
            $path = trim($matches[1]);
            $decimals = isset($matches[2]) ? (int)$matches[2] : 2;
            
            $value = $this->getValueFromPath($path);
            
            if (is_numeric($value)) {
                return number_format((float)$value, $decimals, '.', ',');
            }
            
            return $value;
        }, $template);
        
        return $template;
    }

    /**
     * Process a template fragment with the current data
     *
     * @param string $template
     * @return string
     */
    protected function processTemplate(string $template): string
    {
        $processed = $template;
        
        $processed = $this->processVariables($processed);
        $processed = $this->processEachLoops($processed);
        $processed = $this->processForLoops($processed);
        $processed = $this->processConcat($processed);
        $processed = $this->processFormat($processed);
        
        return $processed;
    }

    /**
     * Get value from data using dot notation path
     *
     * @param string $path
     * @return mixed
     */
    protected function getValueFromPath(string $path)
    {
        $keys = explode('.', $path);
        $value = $this->data;
        
        foreach ($keys as $key) {
            // Handle array indexes like items[0]
            if (preg_match('/^([^\[]+)\[(\d+)\]$/', $key, $matches)) {
                $arrayKey = $matches[1];
                $index = (int)$matches[2];
                
                if (!isset($value[$arrayKey]) || !isset($value[$arrayKey][$index])) {
                    return null;
                }
                
                $value = $value[$arrayKey][$index];
                continue;
            }
            
            if (!is_array($value) || !isset($value[$key])) {
                return null;
            }
            
            $value = $value[$key];
        }
        
        return $value;
    }
}