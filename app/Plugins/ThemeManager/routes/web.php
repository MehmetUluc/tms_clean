<?php

use Illuminate\Support\Facades\Route;
use App\Plugins\ThemeManager\Http\Controllers\ThemeController;

/*
|--------------------------------------------------------------------------
| Theme Manager Routes
|--------------------------------------------------------------------------
|
| Tema yöneticisi ile ilgili web rotaları burada tanımlanır.
|
*/

// Tema CSS rotası
Route::get('/theme-variables.css', [ThemeController::class, 'generateCss'])
    ->name('theme.css');

// Tema ayarları API rotaları
Route::prefix('api')->group(function () {
    Route::get('/theme-settings', [ThemeController::class, 'getThemeSettings'])
        ->name('api.theme-settings');
    
    Route::get('/theme-settings/groups', [ThemeController::class, 'getThemeSettingsByGroup'])
        ->name('api.theme-settings.groups');
});