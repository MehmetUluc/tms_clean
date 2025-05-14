<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Integration API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the Integration plugin.
|
*/

// API Users authentication and verification routes
// Route::prefix('auth')->group(function () {
//     Route::post('/verify', [App\Http\Controllers\Api\AuthController::class, 'verifyApiKey'])
//         ->name('api.auth.verify');
// });

// Mapping routes for API communication
Route::middleware('api.auth')->group(function () {
    // Burada entegrasyon için gerekli API rotaları eklenebilir
    Route::get('/mappings', [App\Http\Controllers\Api\MappingController::class, 'listMappings'])
        ->name('api.mappings.list');
        
    Route::get('/mappings/{id}', [App\Http\Controllers\Api\MappingController::class, 'getMapping'])
        ->name('api.mappings.get');
});