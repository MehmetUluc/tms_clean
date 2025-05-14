<?php

use Illuminate\Support\Facades\Route;

// Vendor API Routes
Route::middleware(['api', 'auth:sanctum'])->prefix(config('vendor.routes.api_prefix'))->name('api.vendors.')->group(function () {
    // Add vendor API routes here
});