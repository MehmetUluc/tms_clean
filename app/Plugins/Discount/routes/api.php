<?php

use Illuminate\Support\Facades\Route;
use App\Plugins\Discount\Http\Controllers\DiscountController;

// API routes for the Discount plugin
Route::middleware(['api'])
    ->prefix('api/discounts')
    ->group(function () {
        // Validate a discount code
        Route::post('/validate-code', [DiscountController::class, 'validateCode'])
            ->name('discounts.validate-code');
        
        // Calculate discounted price
        Route::post('/calculate-price', [DiscountController::class, 'calculatePrice'])
            ->name('discounts.calculate-price');
    });