<?php

use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// RoomAmenity tablosundaki null sort_order değerlerini güncelle
$roomAmenitiesUpdated = DB::table('room_amenities')
    ->whereNull('sort_order')
    ->update(['sort_order' => 0]);

echo "Updated {$roomAmenitiesUpdated} room amenities with null sort_order values\n";

// HotelAmenity tablosundaki null sort_order değerlerini güncelle
$hotelAmenitiesUpdated = DB::table('hotel_amenities')
    ->whereNull('sort_order')
    ->update(['sort_order' => 0]);

echo "Updated {$hotelAmenitiesUpdated} hotel amenities with null sort_order values\n";

echo "Done!\n";