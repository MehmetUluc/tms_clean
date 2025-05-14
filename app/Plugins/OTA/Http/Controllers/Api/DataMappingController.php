<?php

namespace App\Plugins\OTA\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Plugins\OTA\Models\Channel;
use App\Plugins\OTA\Models\DataMapping;
use App\Plugins\OTA\Services\DataMappingService;
use App\Plugins\OTA\Services\TemplateEngine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DataMappingController extends Controller
{
    /**
     * @var DataMappingService
     */
    protected $mappingService;
    
    /**
     * Constructor
     */
    public function __construct(DataMappingService $mappingService)
    {
        $this->mappingService = $mappingService;
    }
    
    /**
     * List all mappings
     */
    public function index(Request $request): JsonResponse
    {
        $mappings = DataMapping::query();
        
        // Filter by channel
        if ($request->has('channel_id')) {
            $mappings->where('channel_id', $request->channel_id);
        }
        
        // Filter by operation type
        if ($request->has('operation_type')) {
            $mappings->where('operation_type', $request->operation_type);
        }
        
        // Filter by format type
        if ($request->has('format_type')) {
            $mappings->where('format_type', $request->format_type);
        }
        
        // Filter by entity
        if ($request->has('mapping_entity')) {
            $mappings->where('mapping_entity', $request->mapping_entity);
        }
        
        // Filter only active mappings
        if ($request->has('active') && $request->active) {
            $mappings->where('is_active', true);
        }
        
        $result = $mappings->with('channel')->get();
        
        return response()->json([
            'data' => $result,
        ]);
    }
    
    /**
     * Show a single mapping
     */
    public function show(DataMapping $mapping): JsonResponse
    {
        return response()->json([
            'data' => $mapping->load('channel'),
        ]);
    }
    
    /**
     * Create a new mapping
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_id' => 'required|exists:channels,id',
            'name' => 'required|string|max:255',
            'operation_type' => 'required|in:import,export',
            'format_type' => 'required|in:xml,json',
            'mapping_entity' => 'required|string|max:50',
            'mapping_data' => 'required|json',
            'template_content' => 'nullable|string',
            'template_format' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:20',
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
        
        // Check for unique combination of channel, operation type, format type and entity
        $exists = DataMapping::where('channel_id', $request->channel_id)
            ->where('operation_type', $request->operation_type)
            ->where('format_type', $request->format_type)
            ->where('mapping_entity', $request->mapping_entity)
            ->exists();
            
        if ($exists) {
            return response()->json([
                'message' => 'A mapping with this channel, operation type, format type and entity already exists',
            ], 422);
        }
        
        $mapping = DataMapping::create($validator->validated());
        
        return response()->json([
            'message' => 'Mapping created successfully',
            'data' => $mapping,
        ], 201);
    }
    
    /**
     * Update mapping information
     */
    public function update(Request $request, DataMapping $mapping): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_id' => 'sometimes|required|exists:channels,id',
            'name' => 'sometimes|required|string|max:255',
            'operation_type' => 'sometimes|required|in:import,export',
            'format_type' => 'sometimes|required|in:xml,json',
            'mapping_entity' => 'sometimes|required|string|max:50',
            'mapping_data' => 'sometimes|required|json',
            'template_content' => 'nullable|string',
            'template_format' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:20',
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
        
        // Check for unique combination if channel, operation type, format type or entity is changing
        if ($request->has(['channel_id', 'operation_type', 'format_type', 'mapping_entity']) && 
            ($mapping->channel_id != $request->channel_id || 
             $mapping->operation_type != $request->operation_type || 
             $mapping->format_type != $request->format_type || 
             $mapping->mapping_entity != $request->mapping_entity)) {
                
            $exists = DataMapping::where('channel_id', $request->channel_id)
                ->where('operation_type', $request->operation_type)
                ->where('format_type', $request->format_type)
                ->where('mapping_entity', $request->mapping_entity)
                ->where('id', '!=', $mapping->id)
                ->exists();
                
            if ($exists) {
                return response()->json([
                    'message' => 'A mapping with this channel, operation type, format type and entity already exists',
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
     * Delete mapping
     */
    public function destroy(DataMapping $mapping): JsonResponse
    {
        $mapping->delete();
        
        return response()->json([
            'message' => 'Mapping deleted successfully',
        ]);
    }
    
    /**
     * Process data from external system
     */
    public function receiveData(Request $request, string $channelCode, string $entity = null): JsonResponse
    {
        // Find the channel
        $channel = Channel::where('slug', $channelCode)->where('is_active', true)->first();
        
        if (!$channel) {
            return response()->json([
                'message' => 'Channel not found or inactive',
            ], 404);
        }
        
        // Check content type and determine format
        $contentType = $request->header('Content-Type');
        $format = null;
        
        if (str_contains($contentType, 'xml')) {
            $format = 'xml';
        } elseif (str_contains($contentType, 'json')) {
            $format = 'json';
        } else {
            // Try to auto-detect format
            $content = $request->getContent();
            $parser = $this->mappingService->detectFormat($content);
            
            if ($parser) {
                $format = $parser->getFormatType();
            } else {
                return response()->json([
                    'message' => 'Unsupported content type. Must be XML or JSON',
                ], 415);
            }
        }
        
        // Get the content
        $content = $request->getContent();
        
        if (empty($content)) {
            return response()->json([
                'message' => 'Empty content received',
            ], 400);
        }
        
        try {
            // Find the mapping
            $query = DataMapping::where('channel_id', $channel->id)
                ->where('operation_type', 'import')
                ->where('format_type', $format)
                ->where('is_active', true);
                
            if ($entity) {
                $query->where('mapping_entity', $entity);
            }
            
            $mapping = $query->first();
            
            if (!$mapping) {
                return response()->json([
                    'message' => 'No active mapping found for this channel, entity and format',
                ], 404);
            }
            
            // Process the data using the appropriate parser
            $parser = $this->mappingService->getParser($format);
            
            if (!$parser) {
                return response()->json([
                    'message' => 'No parser available for format: ' . $format,
                ], 500);
            }
            
            $result = $parser->transform($content, $mapping->mapping_data);
            
            // Update last sync time
            $mapping->last_sync_at = now();
            $mapping->save();
            
            $channel->last_sync_at = now();
            $channel->save();
            
            return response()->json([
                'message' => 'Data received and processed successfully',
                'mapping_id' => $mapping->id,
                'channel' => $channel->name,
                'entity' => $mapping->mapping_entity,
                'format' => $format,
                'content_size' => strlen($content),
                'received_at' => now()->toDateTimeString(),
                'processed_data' => $result,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing received data', [
                'channel' => $channelCode,
                'entity' => $entity,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'message' => 'Error processing data: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Export data to external system
     */
    public function exportData(Request $request, string $channelCode, string $entity = null, string $format = 'xml'): JsonResponse
    {
        // Find the channel
        $channel = Channel::where('slug', $channelCode)->where('is_active', true)->first();
        
        if (!$channel) {
            return response()->json([
                'message' => 'Channel not found or inactive',
            ], 404);
        }
        
        try {
            // Find the mapping
            $query = DataMapping::where('channel_id', $channel->id)
                ->where('operation_type', 'export')
                ->where('format_type', $format)
                ->where('is_active', true);
                
            if ($entity) {
                $query->where('mapping_entity', $entity);
            }
            
            $mapping = $query->first();
            
            if (!$mapping) {
                return response()->json([
                    'message' => 'No active mapping found for this channel, entity and format',
                ], 404);
            }
            
            // In a real implementation, you would:
            // 1. Get data from your system based on the entity type
            // 2. Transform it using the mapping and template
            // 3. Send it to the external system
            
            $sourceData = []; // This would be data from your system
            
            // Use template engine if template content is available
            $transformedData = null;
            
            if (!empty($mapping->template_content)) {
                $templateEngine = new TemplateEngine($mapping->template_content);
                $transformedData = $templateEngine->render($sourceData);
            } else {
                // Use the parser directly if no template is defined
                $parser = $this->mappingService->getParser($format);
                if (!$parser) {
                    return response()->json([
                        'message' => 'No parser available for format: ' . $format,
                    ], 500);
                }
                
                $transformedData = $parser->transform(json_encode($sourceData), $mapping->mapping_data);
            }
            
            // Update last sync time
            $mapping->last_sync_at = now();
            $mapping->save();
            
            $channel->last_sync_at = now();
            $channel->save();
            
            return response()->json([
                'message' => 'Data exported successfully',
                'mapping_id' => $mapping->id,
                'channel' => $channel->name,
                'entity' => $mapping->mapping_entity,
                'format' => $format,
                'export_endpoint' => $channel->export_endpoint,
                'exported_at' => now()->toDateTimeString(),
                'sample_data' => substr((string)$transformedData, 0, 1000) . (strlen((string)$transformedData) > 1000 ? '...' : ''),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error exporting data', [
                'channel' => $channelCode,
                'entity' => $entity,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'message' => 'Error exporting data: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Test mapping configuration
     */
    public function testMapping(Request $request, DataMapping $mapping): JsonResponse
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
            $testData = $request->test_data;
            
            // Get the appropriate parser
            $parser = $this->mappingService->getParser($mapping->format_type);
            
            if (!$parser) {
                return response()->json([
                    'message' => 'No parser available for format: ' . $mapping->format_type,
                ], 500);
            }
            
            // Check if parser can handle this data
            if (!$parser->canParse($testData)) {
                return response()->json([
                    'message' => 'Invalid test data format. Does not match the mapping format type: ' . $mapping->format_type,
                ], 400);
            }
            
            // Transform the data using the mapping
            $transformedData = $parser->transform($testData, $mapping->mapping_data, $mapping->template_content);
            
            return response()->json([
                'message' => 'Test completed successfully',
                'mapping_id' => $mapping->id,
                'channel' => $mapping->channel->name,
                'entity' => $mapping->mapping_entity,
                'test_result' => [
                    'success' => true,
                    'transformed_data' => $transformedData,
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
    
    /**
     * Get available formats
     */
    public function getFormats(): JsonResponse
    {
        return response()->json([
            'data' => $this->mappingService->getAvailableFormats(),
        ]);
    }
    
    /**
     * Get available operations
     */
    public function getOperations(): JsonResponse
    {
        return response()->json([
            'data' => $this->mappingService->getAvailableOperations(),
        ]);
    }
}