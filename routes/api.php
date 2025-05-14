<?php

use App\Http\Controllers\Api\MappingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Plugins\MenuManager\Http\Controllers\Api\MenuController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// XML/JSON Mapping API endpointleri
Route::post('/v1/{endpoint}', [MappingController::class, 'processIncomingData'])
    ->where('endpoint', '.*')
    ->name('api.mapping.process');

// Test endpoint (sadece admin panelden erişilebilir)
Route::get('/test/mapping/{mapping}', [MappingController::class, 'testMapping'])
    ->middleware(['web', 'auth'])
    ->name('api.mapping.test');

// Şema analiz API'leri
// Route::prefix('schema')->middleware(['web', 'auth'])->group(function () {
//     Route::post('/analyze', [App\Http\Controllers\Api\SchemaAnalyzerController::class, 'analyze'])->name('api.schema.analyze');
//     Route::get('/sample-xml', [App\Http\Controllers\Api\SchemaAnalyzerController::class, 'sampleXml'])->name('api.schema.sample-xml');
//     Route::get('/sample-json', [App\Http\Controllers\Api\SchemaAnalyzerController::class, 'sampleJson'])->name('api.schema.sample-json');
// });

// Fiyat İstisnaları API'leri
Route::delete('/price-overrides/{id}', [App\Http\Controllers\PriceOverrideController::class, 'delete'])
    ->middleware(['web', 'auth'])
    ->name('api.price-overrides.delete');

// Menu Manager API Routes
Route::prefix('menu')->group(function () {
    // List all menus
    Route::get('/', [MenuController::class, 'index'])->name('api.menu.index');
    
    // Get a menu by its slug - more specific route needs to come first
    Route::get('/slug/{slug}', [MenuController::class, 'getBySlug'])->name('api.menu.slug');
    
    // Get a menu by its location (header, footer, etc.)
    Route::get('/{location}', [MenuController::class, 'getByLocation'])->name('api.menu.location');
});