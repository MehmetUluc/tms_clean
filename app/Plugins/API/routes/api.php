<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Plugin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the API plugin.
|
*/

// API authentication routes
// Route::prefix('auth')->group(function () {
//     Route::post('/verify', [App\Http\Controllers\Api\AuthController::class, 'verifyApiKey'])
//         ->name('api.auth.verify');
// });

// // API Management routes
// Route::middleware('api.auth')->group(function () {
//     Route::prefix('api-users')->group(function () {
//         Route::get('/', [App\Http\Controllers\Api\ApiUserController::class, 'index'])
//             ->name('api.api-users.index');
//         Route::get('/{id}', [App\Http\Controllers\Api\ApiUserController::class, 'show'])
//             ->name('api.api-users.show');
//     });
    
//     Route::prefix('api-mappings')->group(function () {
//         Route::get('/', [App\Http\Controllers\Api\ApiMappingController::class, 'index'])
//             ->name('api.api-mappings.index');
//         Route::get('/{id}', [App\Http\Controllers\Api\ApiMappingController::class, 'show'])
//             ->name('api.api-mappings.show');
//     });
// });