<?php

use Illuminate\Support\Facades\Route;

// Vendor Web Routes
Route::middleware(['web', 'auth', 'role:vendor'])->prefix(config('vendor.routes.vendor_prefix'))->name('vendor.')->group(function () {
    // Add vendor-specific routes here
});

// Admin Vendor Routes
Route::middleware(['web', 'auth', 'can:vendor_view_any'])->prefix(config('vendor.routes.admin_prefix'))->name('admin.vendors.')->group(function () {
    // Add admin vendor management routes here
});