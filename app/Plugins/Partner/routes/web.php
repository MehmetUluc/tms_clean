<?php

use Illuminate\Support\Facades\Route;

// Partner Web Routes
Route::middleware(['web', 'auth', 'role:partner'])->prefix(config('partner.routes.partner_prefix'))->name('partner.')->group(function () {
    // Add partner-specific routes here
});

// Admin Partner Routes
Route::middleware(['web', 'auth', 'can:partner_view_any'])->prefix(config('partner.routes.admin_prefix'))->name('admin.partners.')->group(function () {
    // Add admin partner management routes here
});