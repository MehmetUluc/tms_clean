<?php

use Illuminate\Support\Facades\Route;
use App\Plugins\MenuManager\Http\Controllers\Api\MenuController;

// API Routes for MenuManager Plugin
Route::group(['prefix' => 'menu'], function () {
    
    // List all menus
    Route::get('/', [MenuController::class, 'index']);
    
    // Get a menu by its slug - more specific route needs to come first
    Route::get('/slug/{slug}', [MenuController::class, 'getBySlug']);
    
    // Get a menu by its location (header, footer, etc.)
    Route::get('/{location}', [MenuController::class, 'getByLocation']);
});