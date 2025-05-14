<?php

use Illuminate\Support\Facades\Route;

// Partner API Routes
Route::middleware(['api', 'auth:sanctum'])->prefix(config('partner.routes.api_prefix'))->name('api.partners.')->group(function () {
    // Add partner API routes here
});