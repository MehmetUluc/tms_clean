<?php

namespace App\Plugins\OTA\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Plugins\OTA\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChannelApiController extends Controller
{
    /**
     * Tüm kanalları listele
     */
    public function index(Request $request): JsonResponse
    {
        $channels = Channel::query();
        
        // Sadece aktif kanalları filtrele
        if ($request->has('active') && $request->active) {
            $channels->where('is_active', true);
        }
        
        // İsime göre filtreleme
        if ($request->has('q')) {
            $channels->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('slug', 'like', '%' . $request->q . '%');
        }
        
        $result = $channels->get();
        
        return response()->json([
            'data' => $result,
        ]);
    }
    
    /**
     * Tek bir kanalı göster
     */
    public function show(Channel $channel): JsonResponse
    {
        return response()->json([
            'data' => $channel->load('mappings'),
        ]);
    }
    
    /**
     * Yeni kanal oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:channels,slug',
            'description' => 'nullable|string',
            'settings.import_endpoint' => 'nullable|url',
            'settings.export_endpoint' => 'nullable|url',
            'credentials.api_key' => 'nullable|string|max:255',
            'credentials.api_secret' => 'nullable|string|max:255',
            'settings.connection_params' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $channel = Channel::create($validator->validated());
        
        return response()->json([
            'message' => 'Channel created successfully',
            'data' => $channel,
        ], 201);
    }
    
    /**
     * Kanalı güncelle
     */
    public function update(Request $request, Channel $channel): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:50|unique:channels,slug,' . $channel->id,
            'description' => 'nullable|string',
            'settings.import_endpoint' => 'nullable|url',
            'settings.export_endpoint' => 'nullable|url',
            'credentials.api_key' => 'nullable|string|max:255',
            'credentials.api_secret' => 'nullable|string|max:255',
            'settings.connection_params' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $channel->update($validator->validated());
        
        return response()->json([
            'message' => 'Channel updated successfully',
            'data' => $channel,
        ]);
    }
    
    /**
     * Kanalı sil
     */
    public function destroy(Channel $channel): JsonResponse
    {
        $channel->delete();
        
        return response()->json([
            'message' => 'Channel deleted successfully',
        ]);
    }
}