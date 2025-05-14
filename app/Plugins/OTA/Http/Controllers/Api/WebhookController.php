<?php

namespace App\Plugins\OTA\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Plugins\OTA\Models\Channel;
use App\Plugins\OTA\Models\DataMapping;
use App\Plugins\OTA\Services\DataMappingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
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
     * Handle webhook endpoint
     */
    public function handle(Request $request, string $channelCode, string $entity = null): JsonResponse
    {
        // Validate API key if provided
        $apiKey = $request->header('X-API-KEY');
        
        // Find the channel
        $channel = Channel::where('slug', $channelCode)->where('is_active', true)->first();
        
        if (!$channel) {
            return response()->json([
                'message' => 'Channel not found or inactive',
            ], 404);
        }
        
        // Check API key if channel requires it
        if (!empty($channel->api_key) && $channel->api_key !== $apiKey) {
            return response()->json([
                'message' => 'Invalid API key',
            ], 401);
        }
        
        // Auto-detect content format
        $content = $request->getContent();
        
        if (empty($content)) {
            return response()->json([
                'message' => 'Empty content received',
            ], 400);
        }
        
        $contentType = $request->header('Content-Type');
        $detectedFormat = null;
        
        if (str_contains($contentType, 'xml')) {
            $detectedFormat = 'xml';
        } elseif (str_contains($contentType, 'json')) {
            $detectedFormat = 'json';
        } else {
            // Try to auto-detect from content
            $parser = $this->mappingService->detectFormat($content);
            
            if ($parser) {
                $detectedFormat = $parser->getFormatType();
            } else {
                return response()->json([
                    'message' => 'Unsupported content format. Unable to detect XML or JSON',
                ], 415);
            }
        }
        
        try {
            // Find appropriate mapping
            $query = DataMapping::where('channel_id', $channel->id)
                ->where('operation_type', 'import')
                ->where('format_type', $detectedFormat)
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
            
            // Process data using the mapping
            $result = $this->mappingService->processData($content, $mapping);
            
            // In a real implementation, you would:
            // 1. Save the transformed data to your database
            // 2. Perform any required business logic
            // 3. Send notifications if needed
            
            // Update last sync time
            $mapping->last_sync_at = now();
            $mapping->save();
            
            $channel->last_sync_at = now();
            $channel->save();
            
            Log::info('Webhook data processed', [
                'channel' => $channelCode,
                'entity' => $entity ?? $mapping->mapping_entity,
                'format' => $detectedFormat,
                'size' => strlen($content),
                'mapping_id' => $mapping->id,
            ]);
            
            return response()->json([
                'message' => 'Webhook data processed successfully',
                'channel' => $channel->name,
                'entity' => $mapping->mapping_entity,
                'format' => $detectedFormat,
                'processed_at' => now()->toDateTimeString(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing webhook data', [
                'channel' => $channelCode,
                'entity' => $entity,
                'format' => $detectedFormat ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'message' => 'Error processing webhook data: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Test webhook with sample data
     */
    public function test(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_code' => 'required|string|exists:channels,slug',
            'entity' => 'nullable|string',
            'format' => 'required|in:xml,json',
            'content' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $channelCode = $request->channel_code;
        $entity = $request->entity;
        $format = $request->format;
        $content = $request->content;
        
        // Find the channel
        $channel = Channel::where('slug', $channelCode)->where('is_active', true)->first();
        
        if (!$channel) {
            return response()->json([
                'message' => 'Channel not found or inactive',
            ], 404);
        }
        
        try {
            // Find appropriate mapping
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
            
            // Validate that the content can be parsed
            $parser = $this->mappingService->getParser($format);
            
            if (!$parser) {
                return response()->json([
                    'message' => 'No parser available for format: ' . $format,
                ], 500);
            }
            
            if (!$parser->canParse($content)) {
                return response()->json([
                    'message' => "Invalid content format. Does not match specified format: $format",
                ], 400);
            }
            
            // Process data using the mapping (but don't save to database - this is just a test)
            $result = $parser->transform($content, $mapping->mapping_data);
            
            return response()->json([
                'message' => 'Test webhook processed successfully',
                'channel' => $channel->name,
                'entity' => $mapping->mapping_entity,
                'format' => $format,
                'processed_at' => now()->toDateTimeString(),
                'test_result' => [
                    'success' => true,
                    'transformed_data' => $result,
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error testing webhook', [
                'channel' => $channelCode,
                'entity' => $entity,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'message' => 'Error testing webhook: ' . $e->getMessage(),
            ], 500);
        }
    }
}