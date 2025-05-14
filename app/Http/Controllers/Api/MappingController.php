<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiMapping;
use App\Models\ApiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MappingController extends Controller
{
    /**
     * Gelen XML/JSON veriyi işle ve hedef modeli güncelle
     */
    public function processIncomingData(Request $request, $endpoint)
    {
        try {
            // Endpoint yolunu normalleştir
            $normalizedEndpoint = $this->normalizeEndpoint($endpoint);
            
            // Mapping kaydını bul
            $mapping = ApiMapping::where('endpoint_path', $normalizedEndpoint)
                ->where('is_active', true)
                ->first();
                
            if (!$mapping) {
                Log::warning("API Mapping bulunamadı: {$normalizedEndpoint}");
                return response()->json(['error' => 'Geçersiz endpoint.'], Response::HTTP_NOT_FOUND);
            }
            
            // API kullanıcısını doğrula
            if (!$this->authenticateApiUser($request, $mapping->apiUser)) {
                Log::warning("API Kimlik doğrulama hatası: {$normalizedEndpoint}");
                return response()->json(['error' => 'Yetkilendirme hatası.'], Response::HTTP_UNAUTHORIZED);
            }
            
            // İstemci IP adresini kontrol et
            $clientIp = $request->ip();
            if (!$mapping->apiUser->checkIpAllowed($clientIp)) {
                Log::warning("API IP erişim engellendi: {$clientIp} için {$normalizedEndpoint}");
                return response()->json(['error' => 'IP adresi erişim engelli.'], Response::HTTP_FORBIDDEN);
            }
            
            // İzinleri kontrol et
            if (!$mapping->apiUser->hasPermission('write')) {
                Log::warning("API İzin hatası: Yazma izni yok. Kullanıcı: {$mapping->apiUser->username}");
                return response()->json(['error' => 'Yazma izniniz yok.'], Response::HTTP_FORBIDDEN);
            }
            
            // Veri türüne göre girdiyi kontrol et
            $contentType = $request->header('Content-Type');
            $rawData = $request->getContent();
            
            if (empty($rawData)) {
                return response()->json(['error' => 'Veri gönderilmedi.'], Response::HTTP_BAD_REQUEST);
            }
            
            // Gelen veriyi işle
            $processedData = $this->processData($rawData, $contentType, $mapping);
            
            // Doğrulama kuralları varsa uygula
            if (!empty($mapping->validation_rules)) {
                $validator = Validator::make($processedData, $mapping->validation_rules);
                
                if ($validator->fails()) {
                    return response()->json([
                        'error' => 'Validasyon hatası.',
                        'details' => $validator->errors(),
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
            
            // Hedef modeli güncelle
            $result = $this->updateTargetModel($mapping, $processedData);
            
            // Son senkronizasyon zamanını güncelle
            $mapping->last_sync_at = now();
            $mapping->save();
            
            // API kullanıcısının son etkinlik zamanını güncelle
            $mapping->apiUser->last_activity_at = now();
            $mapping->apiUser->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Veri başarıyla işlendi.',
                'details' => $result,
            ]);
            
        } catch (\Exception $e) {
            Log::error("API Endpoint işleme hatası: {$e->getMessage()}", [
                'endpoint' => $endpoint,
                'exception' => $e,
            ]);
            
            // Mapping varsa hata bilgisini kaydet
            if (isset($mapping) && $mapping) {
                $mapping->last_error = [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'timestamp' => now()->toDateTimeString(),
                ];
                $mapping->save();
            }
            
            return response()->json([
                'error' => 'İşlem sırasında bir hata oluştu.',
                'message' => config('app.debug') ? $e->getMessage() : 'Sistem hatası.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * API mappingi test etmek için endpoint
     */
    public function testMapping(ApiMapping $mapping, Request $request)
    {
        // Sadece kimliği doğrulanmış kullanıcılar erişebilir
        if (!Auth::check()) {
            return response()->json(['error' => 'Yetkisiz erişim'], Response::HTTP_UNAUTHORIZED);
        }
        
        try {
            // Test verisi veya örnek veri
            $testData = $mapping->test_data ?? $this->generateSampleData($mapping);
            
            // Veri formatına göre işle
            $contentType = $mapping->source_type === 'json' 
                ? 'application/json' 
                : ($mapping->source_type === 'xml' ? 'application/xml' : 'text/plain');
                
            // Veriyi işle
            $processedData = $this->processData($testData, $contentType, $mapping);
            
            return response()->json([
                'status' => 'success',
                'mapping' => $mapping->only(['name', 'source_type', 'target_model', 'target_operation']),
                'input_data' => $testData,
                'processed_data' => $processedData,
                'field_mappings' => $mapping->field_mappings,
                'message' => 'Test başarılı.',
            ]);
            
        } catch (\Exception $e) {
            Log::error("API Mapping test hatası: {$e->getMessage()}", [
                'mapping_id' => $mapping->id,
                'exception' => $e,
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Test sırasında bir hata oluştu.',
                'error' => config('app.debug') ? $e->getMessage() : 'Sistem hatası.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Endpoint yolunu normalize et
     */
    private function normalizeEndpoint(string $endpoint): string
    {
        // Endpoint başında / yoksa ekle
        if (!str_starts_with($endpoint, '/')) {
            $endpoint = '/' . $endpoint;
        }
        
        // api/v1 prefix ekle
        if (!str_starts_with($endpoint, '/api/v1')) {
            $endpoint = '/api/v1' . $endpoint;
        }
        
        return $endpoint;
    }
    
    /**
     * API kullanıcısını kimlik doğrula
     */
    private function authenticateApiUser(Request $request, ApiUser $apiUser): bool
    {
        // Eğer kullanıcı aktif değilse kimlik doğrulama başarısız
        if (!$apiUser->is_active) {
            return false;
        }
        
        // HTTP Basic Authentication kontrolü
        $credentials = $request->getUser();
        $apiKey = $request->getPassword();
        
        if ($credentials && $apiKey) {
            return $apiUser->username === $credentials && $apiUser->api_key === $apiKey;
        }
        
        // Alternatif olarak, API key header kontrolü
        $apiKeyHeader = $request->header('X-API-KEY');
        if ($apiKeyHeader && $apiUser->api_key === $apiKeyHeader) {
            // Username header veya username parametresi kontrolü
            $usernameHeader = $request->header('X-USERNAME') ?? $request->input('username');
            if ($usernameHeader && $apiUser->username === $usernameHeader) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Veriyi işle
     */
    private function processData(string $rawData, ?string $contentType, ApiMapping $mapping): array
    {
        // İçerik tipine göre veriyi parse et
        $parsedData = [];
        
        if ($contentType && str_contains($contentType, 'json')) {
            $parsedData = json_decode($rawData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Geçersiz JSON formatı: ' . json_last_error_msg());
            }
        } elseif ($contentType && str_contains($contentType, 'xml')) {
            $xml = simplexml_load_string($rawData);
            if ($xml === false) {
                throw new \Exception('Geçersiz XML formatı');
            }
            $parsedData = json_decode(json_encode($xml), true);
        } else {
            // Diğer format türleri (CSV vb.)
            // Şimdilik basitçe JSON olarak yorumlamayı deneyelim
            $parsedData = json_decode($rawData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Desteklenmeyen veri formatı');
            }
        }
        
        // Mapping tanımını kullanarak verileri işle
        return $mapping->processIncomingData($parsedData);
    }
    
    /**
     * Hedef modeli güncelle
     */
    private function updateTargetModel(ApiMapping $mapping, array $processedData): array
    {
        $modelClass = $mapping->target_model;
        $operation = $mapping->target_operation;
        
        switch ($operation) {
            case 'create':
                $model = new $modelClass();
                foreach ($processedData as $key => $value) {
                    $model->$key = $value;
                }
                $model->save();
                
                return [
                    'operation' => 'create',
                    'model' => class_basename($modelClass),
                    'id' => $model->id,
                ];
                
            case 'update':
                // Güncellenecek kayıtları bulma kriterleri
                $identifiers = $this->extractIdentifiers($processedData);
                if (empty($identifiers)) {
                    throw new \Exception('Güncellenecek kayıt için tanımlayıcı bulunamadı');
                }
                
                $query = $modelClass::query();
                foreach ($identifiers as $field => $value) {
                    $query->where($field, $value);
                }
                
                $model = $query->first();
                if (!$model) {
                    throw new \Exception('Güncellenecek kayıt bulunamadı');
                }
                
                // Model alanlarını güncelle
                foreach ($processedData as $key => $value) {
                    // Tanımlayıcı alanları hariç diğer alanları güncelle
                    if (!array_key_exists($key, $identifiers)) {
                        $model->$key = $value;
                    }
                }
                $model->save();
                
                return [
                    'operation' => 'update',
                    'model' => class_basename($modelClass),
                    'id' => $model->id,
                ];
                
            case 'upsert':
                // Güncellenecek kayıtları bulma kriterleri
                $identifiers = $this->extractIdentifiers($processedData);
                if (empty($identifiers)) {
                    throw new \Exception('Güncelleme/oluşturma için tanımlayıcı bulunamadı');
                }
                
                $query = $modelClass::query();
                foreach ($identifiers as $field => $value) {
                    $query->where($field, $value);
                }
                
                $model = $query->first();
                
                // Model yoksa oluştur, varsa güncelle
                if (!$model) {
                    $model = new $modelClass();
                    foreach ($processedData as $key => $value) {
                        $model->$key = $value;
                    }
                    $model->save();
                    
                    return [
                        'operation' => 'create',
                        'model' => class_basename($modelClass),
                        'id' => $model->id,
                    ];
                } else {
                    // Model alanlarını güncelle
                    foreach ($processedData as $key => $value) {
                        $model->$key = $value;
                    }
                    $model->save();
                    
                    return [
                        'operation' => 'update',
                        'model' => class_basename($modelClass),
                        'id' => $model->id,
                    ];
                }
                
            case 'delete':
                // Silinecek kayıtları bulma kriterleri
                $identifiers = $this->extractIdentifiers($processedData);
                if (empty($identifiers)) {
                    throw new \Exception('Silinecek kayıt için tanımlayıcı bulunamadı');
                }
                
                $query = $modelClass::query();
                foreach ($identifiers as $field => $value) {
                    $query->where($field, $value);
                }
                
                $model = $query->first();
                if (!$model) {
                    throw new \Exception('Silinecek kayıt bulunamadı');
                }
                
                $modelId = $model->id;
                $model->delete();
                
                return [
                    'operation' => 'delete',
                    'model' => class_basename($modelClass),
                    'id' => $modelId,
                ];
                
            case 'custom':
                // Özel işlem sınıfını çalıştır
                // Bu kısım geliştirilecek
                return [
                    'operation' => 'custom',
                    'model' => class_basename($modelClass),
                    'data' => $processedData,
                ];
                
            default:
                throw new \Exception('Desteklenmeyen işlem türü: ' . $operation);
        }
    }
    
    /**
     * İşlem için tanımlayıcı alanları çıkar
     */
    private function extractIdentifiers(array $data): array
    {
        $identifiers = [];
        
        // id alanı varsa onu tanımlayıcı olarak kullan
        if (isset($data['id'])) {
            $identifiers['id'] = $data['id'];
        }
        
        // external_id alanı varsa onu tanımlayıcı olarak kullan
        if (isset($data['external_id'])) {
            $identifiers['external_id'] = $data['external_id'];
        }
        
        // Diğer yaygın tanımlayıcı alanları
        $possibleIdentifiers = ['uuid', 'code', 'sku', 'room_id', 'hotel_id'];
        foreach ($possibleIdentifiers as $field) {
            if (isset($data[$field])) {
                $identifiers[$field] = $data[$field];
            }
        }
        
        return $identifiers;
    }
    
    /**
     * Test için örnek veri oluştur
     */
    private function generateSampleData(ApiMapping $mapping): string
    {
        if ($mapping->source_type === 'json') {
            $sampleData = [
                'id' => 1,
                'name' => 'Örnek Veri',
                'attributes' => [
                    'price' => 100,
                    'quantity' => 5,
                ],
                'children' => [
                    ['id' => 1, 'name' => 'Alt Öğe 1'],
                    ['id' => 2, 'name' => 'Alt Öğe 2'],
                ],
            ];
            
            return json_encode($sampleData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } elseif ($mapping->source_type === 'xml') {
            return '<?xml version="1.0" encoding="UTF-8"?>
<root>
    <id>1</id>
    <name>Örnek Veri</name>
    <attributes>
        <price>100</price>
        <quantity>5</quantity>
    </attributes>
    <children>
        <child>
            <id>1</id>
            <name>Alt Öğe 1</name>
        </child>
        <child>
            <id>2</id>
            <name>Alt Öğe 2</name>
        </child>
    </children>
</root>';
        } else {
            // CSV örneği
            return "id,name,price,quantity\n1,Örnek Veri,100,5";
        }
    }
}
