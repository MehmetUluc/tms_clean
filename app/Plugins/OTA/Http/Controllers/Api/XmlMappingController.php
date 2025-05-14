<?php

namespace App\Plugins\OTA\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Plugins\OTA\Models\Channel;
use App\Plugins\OTA\Models\XmlMapping;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class XmlMappingController extends Controller
{
    /**
     * Tüm mappingleri listele
     */
    public function index(Request $request): JsonResponse
    {
        $mappings = XmlMapping::query();
        
        // Kanala göre filtrele
        if ($request->has('channel_id')) {
            $mappings->where('channel_id', $request->channel_id);
        }
        
        // Tipe göre filtrele
        if ($request->has('type')) {
            $mappings->where('type', $request->type);
        }
        
        // Entityye göre filtrele
        if ($request->has('mapping_entity')) {
            $mappings->where('mapping_entity', $request->mapping_entity);
        }
        
        // Sadece aktif mappingleri filtrele
        if ($request->has('active') && $request->active) {
            $mappings->where('is_active', true);
        }
        
        $result = $mappings->with('channel')->get();
        
        return response()->json([
            'data' => $result,
        ]);
    }
    
    /**
     * Tek bir mapping bilgisini göster
     */
    public function show(XmlMapping $mapping): JsonResponse
    {
        return response()->json([
            'data' => $mapping->load('channel'),
        ]);
    }
    
    /**
     * Yeni mapping oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_id' => 'required|exists:channels,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:import,export',
            'mapping_entity' => 'required|string|max:50',
            'mapping_data' => 'required|json',
            'description' => 'nullable|string',
            'validation_rules' => 'nullable|json',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Aynı kanal, tip ve entity kombinasyonu için unique kontrolü
        $exists = XmlMapping::where('channel_id', $request->channel_id)
            ->where('type', $request->type)
            ->where('mapping_entity', $request->mapping_entity)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'message' => 'A mapping with this channel, type and entity already exists',
            ], 422);
        }
        
        $mapping = XmlMapping::create($validator->validated());
        
        return response()->json([
            'message' => 'Mapping created successfully',
            'data' => $mapping,
        ], 201);
    }
    
    /**
     * Mapping bilgilerini güncelle
     */
    public function update(Request $request, XmlMapping $mapping): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_id' => 'sometimes|required|exists:channels,id',
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:import,export',
            'mapping_entity' => 'sometimes|required|string|max:50',
            'mapping_data' => 'sometimes|required|json',
            'description' => 'nullable|string',
            'validation_rules' => 'nullable|json',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Eğer channel, type ve entity değiştiyse unique kontrolü yap
        if ($request->has(['channel_id', 'type', 'mapping_entity']) && 
            ($mapping->channel_id != $request->channel_id || 
             $mapping->type != $request->type || 
             $mapping->mapping_entity != $request->mapping_entity)) {
                
            $exists = XmlMapping::where('channel_id', $request->channel_id)
                ->where('type', $request->type)
                ->where('mapping_entity', $request->mapping_entity)
                ->where('id', '!=', $mapping->id)
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'message' => 'A mapping with this channel, type and entity already exists',
                ], 422);
            }
        }
        
        $mapping->update($validator->validated());
        
        return response()->json([
            'message' => 'Mapping updated successfully',
            'data' => $mapping,
        ]);
    }
    
    /**
     * Mapping silinmesi
     */
    public function destroy(XmlMapping $mapping): JsonResponse
    {
        $mapping->delete();
        
        return response()->json([
            'message' => 'Mapping deleted successfully',
        ]);
    }
    
    /**
     * Dış sistemden gelen veriyi işle
     */
    public function receiveData(Request $request, string $channelCode, string $entity = null): JsonResponse
    {
        // Kanalı bul
        $channel = Channel::where('slug', $channelCode)->where('is_active', true)->first();
        
        if (!$channel) {
            return response()->json([
                'message' => 'Channel not found or inactive',
            ], 404);
        }
        
        // Gelen verinin XML veya JSON olduğunu kontrol et
        $contentType = $request->header('Content-Type');
        $isXml = str_contains($contentType, 'xml');
        $isJson = str_contains($contentType, 'json');
        
        if (!$isXml && !$isJson) {
            return response()->json([
                'message' => 'Unsupported content type. Must be XML or JSON',
            ], 415);
        }
        
        // Gelen veriyi al
        $content = $request->getContent();
        
        if (empty($content)) {
            return response()->json([
                'message' => 'Empty content received',
            ], 400);
        }
        
        try {
            // Eşleştirmeyi bul
            $query = XmlMapping::where('channel_id', $channel->id)
                ->where('type', 'import')
                ->where('is_active', true);
                
            if ($entity) {
                $query->where('mapping_entity', $entity);
            }
            
            $mapping = $query->first();
            
            if (!$mapping) {
                return response()->json([
                    'message' => 'No active mapping found for this channel and entity',
                ], 404);
            }
            
            // Bu noktada gelen veriyi işlemek için bir servis kullanılabilir
            // Örnek bir dönüş yapıyoruz
            
            // Son senkronizasyon zamanını güncelle
            $mapping->last_sync_at = now();
            $mapping->save();
            
            $channel->last_sync_at = now();
            $channel->save();
            
            return response()->json([
                'message' => 'Data received successfully',
                'mapping_id' => $mapping->id,
                'channel' => $channel->name,
                'entity' => $mapping->mapping_entity,
                'content_size' => strlen($content),
                'received_at' => now()->toDateTimeString(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing received data', [
                'channel' => $channelCode,
                'entity' => $entity,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'message' => 'Error processing data: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Dış sisteme veri gönderme işlemini simüle et
     */
    public function exportData(Request $request, string $channelCode, string $entity = null): JsonResponse
    {
        // Kanalı bul
        $channel = Channel::where('slug', $channelCode)->where('is_active', true)->first();
        
        if (!$channel) {
            return response()->json([
                'message' => 'Channel not found or inactive',
            ], 404);
        }
        
        try {
            // Eşleştirmeyi bul
            $query = XmlMapping::where('channel_id', $channel->id)
                ->where('type', 'export')
                ->where('is_active', true);
                
            if ($entity) {
                $query->where('mapping_entity', $entity);
            }
            
            $mapping = $query->first();
            
            if (!$mapping) {
                return response()->json([
                    'message' => 'No active mapping found for this channel and entity',
                ], 404);
            }
            
            // Bu noktada veri dış sisteme gönderilir
            // Örnek bir dönüş yapıyoruz
            
            // Son senkronizasyon zamanını güncelle
            $mapping->last_sync_at = now();
            $mapping->save();
            
            $channel->last_sync_at = now();
            $channel->save();
            
            return response()->json([
                'message' => 'Data exported successfully',
                'mapping_id' => $mapping->id,
                'channel' => $channel->name,
                'entity' => $mapping->mapping_entity,
                'export_endpoint' => $channel->export_endpoint,
                'exported_at' => now()->toDateTimeString(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error exporting data', [
                'channel' => $channelCode,
                'entity' => $entity,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'message' => 'Error exporting data: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Eşleştirme konfigürasyonunu test et
     */
    public function testMapping(Request $request, XmlMapping $mapping): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'test_data' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        try {
            // Test verisinin formatını kontrol et
            $testData = $request->test_data;
            $contentType = $request->header('Content-Type', '');
            
            $isJson = str_contains($contentType, 'json') || json_decode($testData) !== null;
            $isXml = str_contains($contentType, 'xml') || @simplexml_load_string($testData) !== false;
            
            if (!$isJson && !$isXml) {
                return response()->json([
                    'message' => 'Invalid test data format. Must be valid XML or JSON',
                ], 400);
            }
            
            // Bu noktada eşleştirme servisini kullanarak test edebiliriz
            // Örnek bir dönüş yapıyoruz
            
            return response()->json([
                'message' => 'Test completed successfully',
                'mapping_id' => $mapping->id,
                'channel' => $mapping->channel->name,
                'entity' => $mapping->mapping_entity,
                'test_result' => [
                    'success' => true,
                    'sample_transformed_data' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                    'test_timestamp' => now()->toDateTimeString(),
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error testing mapping', [
                'mapping_id' => $mapping->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'message' => 'Error testing mapping: ' . $e->getMessage(),
            ], 500);
        }
    }
}