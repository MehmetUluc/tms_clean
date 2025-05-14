<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SimpleXMLElement;

class XmlSchemaAnalyzer
{
    protected array $schema = [];
    protected array $paths = [];
    protected array $examples = [];
    protected ?string $xml = null;
    
    /**
     * XML metnini kullanarak şema analizi yap
     */
    public function analyzeFromXml(string $xml): array
    {
        try {
            $this->xml = $xml;
            
            $xmlObj = simplexml_load_string($xml);
            if ($xmlObj === false) {
                throw new \Exception("Geçersiz XML formatı");
            }
            
            // XML yapısını array'e dönüştür
            $data = json_decode(json_encode($xmlObj), true);
            
            // Şema çıkarımı yap
            $this->extractSchema($data);
            
            return [
                'paths' => $this->getPaths(),
                'schema' => $this->getSchema(),
                'examples' => $this->getExamples(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'paths' => [],
                'schema' => [],
                'examples' => [],
            ];
        }
    }
    
    /**
     * JSON verisini kullanarak şema analizi yap
     */
    public function analyzeFromJson(string $json): array
    {
        try {
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Geçersiz JSON formatı: " . json_last_error_msg());
            }
            
            // Şema çıkarımı yap
            $this->extractSchema($data);
            
            return [
                'paths' => $this->getPaths(),
                'schema' => $this->getSchema(),
                'examples' => $this->getExamples(),
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'paths' => [],
                'schema' => [],
                'examples' => [],
            ];
        }
    }
    
    /**
     * Bir veri yapısından şema çıkarımı yap
     */
    protected function extractSchema(array $data, string $prefix = ''): void
    {
        foreach ($data as $key => $value) {
            $currentPath = $prefix ? $prefix . '.' . $key : $key;
            
            // Sayısal indeksli dizileri tespit et (array of objects)
            if (is_numeric($key) && $prefix) {
                // Array of objects yapısı - parent yoluna döneriz
                $arrayPath = $prefix;
                
                // Bu dizinin bir örnek elemanını sakla
                $this->addExample($arrayPath, $value);
                
                // Dizinin içindeki yapıyı analiz et
                if (is_array($value)) {
                    $this->extractSchema($value, $arrayPath . '.*');
                } else {
                    $this->addPath($arrayPath . '.*', gettype($value));
                }
                
                continue;
            }
            
            if (is_array($value)) {
                // İç içe dizi yapısı
                $this->addPath($currentPath, 'array');
                $this->extractSchema($value, $currentPath);
            } else {
                // Temel değer
                $this->addPath($currentPath, $this->determineType($value));
                $this->addExample($currentPath, $value);
            }
        }
    }
    
    /**
     * Dönen yolları standartlaştır ve döndür
     */
    public function getPaths(): array
    {
        $formattedPaths = [];
        
        foreach ($this->paths as $path => $type) {
            // Array of objects yapıları için özel işlem
            if (Str::endsWith($path, '.*')) {
                $parentPath = Str::beforeLast($path, '.*');
                $formattedPaths[$path] = [
                    'path' => $path,
                    'type' => 'array of ' . $type,
                    'parent' => $parentPath,
                    'isArrayItem' => true,
                ];
            } else {
                $formattedPaths[$path] = [
                    'path' => $path,
                    'type' => $type,
                    'parent' => Str::contains($path, '.') ? Str::beforeLast($path, '.') : null,
                    'isArrayItem' => false,
                ];
            }
        }
        
        // Yolları doğal sırayla sırala
        ksort($formattedPaths);
        
        return $formattedPaths;
    }
    
    /**
     * Şemayı döndür
     */
    public function getSchema(): array
    {
        return $this->schema;
    }
    
    /**
     * Örnekleri döndür
     */
    public function getExamples(): array
    {
        return $this->examples;
    }
    
    /**
     * Değerin veri tipini belirle
     */
    protected function determineType($value): string
    {
        if (is_null($value)) {
            return 'null';
        }
        
        if (is_bool($value)) {
            return 'boolean';
        }
        
        if (is_numeric($value)) {
            return is_float($value) ? 'float' : 'integer';
        }
        
        // Tarih formatı kontrolü
        if (is_string($value)) {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return 'date';
            }
            
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                return 'datetime';
            }
        }
        
        return 'string';
    }
    
    /**
     * Bir yolu ve tipini ekle
     */
    protected function addPath(string $path, string $type): void
    {
        $this->paths[$path] = $type;
        
        // Şemaya da ekle
        Arr::set($this->schema, $path, [
            'type' => $type,
            'path' => $path,
        ]);
    }
    
    /**
     * Bir yol için örnek değer ekle
     */
    protected function addExample(string $path, $value): void
    {
        if (is_array($value)) {
            $this->examples[$path] = '[Complex Object]';
        } else {
            $this->examples[$path] = $value;
        }
    }
    
    /**
     * XML kaynak çevirisi yap
     */
    public function convertXPathToPath(string $xpath): string
    {
        $xpath = str_replace('/', '.', $xpath);
        $xpath = ltrim($xpath, '.');
        return $xpath;
    }
    
    /**
     * XML içinden belirli bir XPath yolundaki örnek bir değeri çek
     */
    public function getValueByXPath(string $xpath): string
    {
        if (!$this->xml) {
            return 'N/A';
        }
        
        try {
            $xml = new SimpleXMLElement($this->xml);
            $result = $xml->xpath($xpath);
            
            if (empty($result)) {
                return 'Bulunamadı';
            }
            
            return (string) $result[0];
        } catch (\Exception $e) {
            return 'Hata: ' . $e->getMessage();
        }
    }
}