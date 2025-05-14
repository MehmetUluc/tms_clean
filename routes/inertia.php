<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inertia\HomeController;
use App\Http\Controllers\Inertia\HotelController;
use App\Http\Controllers\Inertia\DestinationController;

// Inertia B2C Theme Routes - Root path is defined in web.php

// Static Pages
Route::get('/about', [HomeController::class, 'about'])->name('inertia.about');
Route::get('/contact', [HomeController::class, 'contact'])->name('inertia.contact');
Route::get('/terms', [HomeController::class, 'terms'])->name('inertia.terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('inertia.privacy');
Route::get('/faq', [HomeController::class, 'faq'])->name('inertia.faq');

// Destinations (Regions) Pages
Route::get('/destinations', [DestinationController::class, 'index'])->name('inertia.destinations.index');
Route::get('/destinations/{destination}', [DestinationController::class, 'show'])->name('inertia.destinations.show');

// Hotel Pages - Note: Main routes are defined in web.php now
// These routes will serve as aliases to maintain backward compatibility
Route::get('/inertia-hotels', [HotelController::class, 'index'])->name('inertia.hotels.index');
Route::get('/inertia-hotels/{hotel}', [HotelController::class, 'show'])->name('inertia.hotels.show'); 
Route::get('/inertia-hotels/{hotel}/rooms', [HotelController::class, 'rooms'])->name('inertia.hotels.rooms');