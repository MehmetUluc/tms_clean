<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\XmlMappingController;
use App\Http\Controllers\B2C\HomeController;
use App\Http\Controllers\B2C\HotelController;
use App\Http\Controllers\B2C\RegionController;
use App\Http\Controllers\B2C\BookingController;
use App\Http\Controllers\B2C\UserController;

// Define root route to use our unified frontend theme
Route::get('/', function() {
    return view('frontend.pages.home');
})->name('home');

// Test route for debugging
Route::get('/test-hotels', [App\Http\Controllers\TestController::class, 'index']);

// Static Pages (redirect to Inertia routes)
Route::get('/about', function() { 
    return redirect()->route('inertia.about');
})->name('about');
Route::get('/contact', function() { 
    return redirect()->route('inertia.contact');
})->name('contact');
Route::get('/terms', function() { 
    return redirect()->route('inertia.terms');
})->name('terms');
Route::get('/privacy', function() { 
    return redirect()->route('inertia.privacy');
})->name('privacy');
Route::get('/faq', function() { 
    return redirect()->route('inertia.faq');
})->name('faq');

// Region Pages (redirect to Inertia destinations routes)
Route::get('/regions', function() {
    return redirect()->route('inertia.destinations.index');
})->name('regions.index');

Route::get('/regions/{slug}', function($slug) {
    return redirect()->route('inertia.destinations.show', $slug);
})->name('regions.show');

// Hotel Pages (direct routes instead of redirects)
Route::get('/hotels', [App\Http\Controllers\Inertia\HotelController::class, 'index'])->name('hotels.index');

Route::get('/hotels/{hotel}', [App\Http\Controllers\Inertia\HotelController::class, 'show'])->name('hotels.show');

Route::get('/hotels/{hotel}/rooms', [App\Http\Controllers\Inertia\HotelController::class, 'rooms'])->name('hotels.rooms');

// Booking Process
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/search', function() { 
        return '<h1>Search Results</h1><p>This is a placeholder for the booking search results page</p>'; 
    })->name('search');
    Route::get('/hotel/{slug}', function($slug) { 
        return '<h1>Book Hotel: ' . $slug . '</h1><p>This is a placeholder for the hotel booking page</p>'; 
    })->name('hotel');
    Route::get('/room/{id}', function($id) { 
        return '<h1>Book Room #' . $id . '</h1><p>This is a placeholder for the room booking page</p>'; 
    })->name('room');
    Route::get('/guest-info', function() { 
        return '<h1>Guest Information</h1><p>This is a placeholder for the guest information page</p>'; 
    })->name('guest-info');
    Route::get('/payment', function() { 
        return '<h1>Payment</h1><p>This is a placeholder for the payment page</p>'; 
    })->name('payment');
    Route::get('/confirmation/{id}', function($id) { 
        return '<h1>Booking Confirmation #' . $id . '</h1><p>This is a placeholder for the booking confirmation page</p>'; 
    })->name('confirmation');
});

// User Profile Pages (auth middleware protected)
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', function() { 
        return '<h1>User Profile</h1><p>This is a placeholder for the user profile page</p>'; 
    })->name('index');
    Route::get('/reservations', function() { 
        return '<h1>Reservations</h1><p>This is a placeholder for the user reservations page</p>'; 
    })->name('reservations');
    Route::get('/reservations/{id}', function($id) { 
        return '<h1>Reservation #' . $id . '</h1><p>This is a placeholder for a specific reservation</p>'; 
    })->name('reservations.show');
    Route::get('/settings', function() { 
        return '<h1>User Settings</h1><p>This is a placeholder for the user settings page</p>'; 
    })->name('settings');
});

// Auth routes (simplified placeholders)
Route::get('/login', function() { 
    return '<h1>Login Page</h1><p>This is a placeholder for the login page</p>'; 
})->name('login');

Route::get('/register', function() { 
    return '<h1>Register Page</h1><p>This is a placeholder for the registration page</p>'; 
})->name('register');

Route::post('/logout', function() { 
    return redirect('/'); 
})->name('logout');

// XML/JSON mapping tool routes
Route::get('/xml-mapper', [XmlMappingController::class, 'index'])->name('xml-mapper.index');
Route::post('/xml-mapper/analyze', [XmlMappingController::class, 'analyze'])->name('xml-mapper.analyze');

// Mega Menu Demo route
Route::get('/mega-menu-demo/{menu}', function($menu) {
    $menuModel = \App\Plugins\MenuManager\Models\Menu::where('slug', $menu)
        ->orWhere('id', $menu)
        ->with(['items' => function ($query) {
            $query->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->with('children');
        }])
        ->firstOrFail();
        
    return view('vendor.menu-manager.demo-mega-menu', [
        'menu' => $menuModel
    ]);
})->name('menu-manager.mega-demo');