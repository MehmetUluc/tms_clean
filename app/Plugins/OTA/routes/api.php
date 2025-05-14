<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Plugins\OTA\Http\Controllers\Api\ChannelApiController;
use App\Plugins\OTA\Http\Controllers\Api\XmlMappingController;
use App\Plugins\OTA\Http\Controllers\Api\DataMappingController;
use App\Plugins\OTA\Http\Controllers\Api\WebhookController;

/*
|--------------------------------------------------------------------------
| OTA API Routes
|--------------------------------------------------------------------------
|
| OTA integration API routes.
|
*/

// OTA API endpoints
Route::prefix('ota')->name('api.ota.')->group(function () {
    // Channel operations
    Route::apiResource('channels', ChannelApiController::class);
    
    // Legacy XML Mapping operations (for backward compatibility)
    Route::apiResource('xml-mappings', XmlMappingController::class);
    
    // New Data Mapping operations
    Route::apiResource('mappings', DataMappingController::class);
    
    // Get available formats and operations
    Route::get('formats', [DataMappingController::class, 'getFormats'])
        ->name('formats');
    Route::get('operations', [DataMappingController::class, 'getOperations'])
        ->name('operations');
    
    // Data receiver endpoint - for receiving data from external systems
    Route::post('receive/{channel}/{entity?}', [DataMappingController::class, 'receiveData'])
        ->name('receive');
        
    // Data export endpoint - for sending data to external systems
    Route::post('export/{channel}/{entity?}/{format?}', [DataMappingController::class, 'exportData'])
        ->name('export');
        
    // Test endpoint
    Route::post('test-mapping/{mapping}', [DataMappingController::class, 'testMapping'])
        ->name('test-mapping');
        
    // Webhook endpoints
    Route::post('webhook/{channel}/{entity?}', [WebhookController::class, 'handle'])
        ->name('webhook');
    Route::post('webhook-test', [WebhookController::class, 'test'])
        ->name('webhook.test');
});