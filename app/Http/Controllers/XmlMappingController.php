<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class XmlMappingController extends Controller
{
    /**
     * Bir XML veya JSON ayrıştırma aracı sağlar
     */
    public function index()
    {
        return view('xml-mapper.index');
    }
    
    /**
     * XML veriyi analiz et ve yapıyı çıkar
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'data' => 'required|string',
            'format' => 'required|in:xml,json',
        ]);
        
        $data = $request->input('data');
        $format = $request->input('format');
        
        try {
            $structure = [];
            $paths = [];
            
            if ($format === 'xml') {
                $xml = new SimpleXMLElement($data);
                $structure = $this->xmlToArray($xml);
                $paths = $this->extractPaths($structure);
            } else {
                $json = json_decode($data, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Geçersiz JSON: ' . json_last_error_msg());
                }
                $structure = $json;
                $paths = $this->extractPaths($json);
            }
            
            // View'a veri gönderelim
            return view('xml-mapper.result', [
                'structure' => $structure,
                'paths' => $paths,
                'sourceData' => $data,
                'format' => $format
            ]);
            
        } catch (\Exception $e) {
            Log::error('XML/JSON analiz hatası: ' . $e->getMessage());
            
            // Hata durumunda geri dönelim ve hatayı gösterelim
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * XML'i dizi yapısına dönüştürür
     */
    private function xmlToArray(SimpleXMLElement $xml): array
    {
        $array = [];
        
        // Öznitelikleri işle
        foreach ($xml->attributes() as $name => $value) {
            $array['@attributes'][$name] = (string) $value;
        }
        
        // Alt öğeleri işle
        foreach ($xml->children() as $name => $element) {
            // Aynı isimde çoklu öğe kontrolü
            if (isset($array[$name])) {
                if (!is_array($array[$name]) || !isset($array[$name][0])) {
                    $array[$name] = [$array[$name]];
                }
                $array[$name][] = $this->xmlToArray($element);
            } else {
                $array[$name] = $this->xmlToArray($element);
            }
        }
        
        // Metin içeriğini işle
        if (trim((string) $xml) !== '') {
            if (empty($array)) {
                return (string) $xml;
            } else {
                $array['@text'] = (string) $xml;
            }
        }
        
        return $array;
    }
    
    /**
     * Veri yapısından tüm muhtemel yolları çıkarır
     */
    private function extractPaths($data, $prefix = '', $paths = []): array
    {
        if (!is_array($data)) {
            $paths[] = $prefix;
            return $paths;
        }
        
        foreach ($data as $key => $value) {
            $currentPath = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value)) {
                // Dizi içinde dizi kontrolü (çoklu öğeler)
                if (isset($value[0]) && is_array($value[0])) {
                    // Her bir öğe için yolları çıkar
                    $arrayPath = $currentPath . '.*';
                    $sampleItem = $value[0];
                    $paths = $this->extractPaths($sampleItem, $arrayPath, $paths);
                } else {
                    // Normal iç içe yapı
                    $paths = $this->extractPaths($value, $currentPath, $paths);
                }
            } else {
                $paths[] = $currentPath;
            }
        }
        
        return $paths;
    }
}
