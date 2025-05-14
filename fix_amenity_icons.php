<?php

use App\Plugins\Amenities\Models\RoomAmenity;
use App\Plugins\Amenities\Models\HotelAmenity;
use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Heroicons listesi - form'da kullandığımız ikonlar
$validIcons = [
    'heroicon-o-academic-cap', 'heroicon-o-adjustments-horizontal', 'heroicon-o-arrow-down',
    'heroicon-o-arrow-left', 'heroicon-o-arrow-right', 'heroicon-o-arrow-up', 'heroicon-o-bell',
    'heroicon-o-bolt', 'heroicon-o-book-open', 'heroicon-o-building-office', 'heroicon-o-building-storefront',
    'heroicon-o-cake', 'heroicon-o-calculator', 'heroicon-o-calendar', 'heroicon-o-camera',
    'heroicon-o-chart-bar', 'heroicon-o-check', 'heroicon-o-check-circle', 'heroicon-o-clock',
    'heroicon-o-cloud', 'heroicon-o-cog', 'heroicon-o-credit-card', 'heroicon-o-currency-dollar',
    'heroicon-o-device-phone-mobile', 'heroicon-o-envelope', 'heroicon-o-eye', 'heroicon-o-face-smile',
    'heroicon-o-film', 'heroicon-o-fire', 'heroicon-o-flag', 'heroicon-o-gift', 'heroicon-o-globe-alt',
    'heroicon-o-hand-thumb-up', 'heroicon-o-heart', 'heroicon-o-home', 'heroicon-o-key',
    'heroicon-o-light-bulb', 'heroicon-o-link', 'heroicon-o-map', 'heroicon-o-map-pin',
    'heroicon-o-musical-note', 'heroicon-o-pencil', 'heroicon-o-phone', 'heroicon-o-photo',
    'heroicon-o-plus', 'heroicon-o-puzzle-piece', 'heroicon-o-rocket-launch', 'heroicon-o-server',
    'heroicon-o-shield-check', 'heroicon-o-shopping-bag', 'heroicon-o-shopping-cart',
    'heroicon-o-sparkles', 'heroicon-o-star', 'heroicon-o-sun', 'heroicon-o-tag', 'heroicon-o-truck',
    'heroicon-o-tv', 'heroicon-o-user', 'heroicon-o-users', 'heroicon-o-video-camera',
    'heroicon-o-wifi', 'heroicon-o-wrench', 'heroicon-o-x-circle',
];

// İkon isimleri için dönüşüm tablosu
$iconMappings = [
    'air-conditioner' => 'heroicon-o-sun',
    'television' => 'heroicon-o-tv',
    'wifi' => 'heroicon-o-wifi',
    'bath' => 'heroicon-o-sparkles',
    'shower' => 'heroicon-o-sparkles',
    'refrigerator' => 'heroicon-o-cube',
    'kitchen' => 'heroicon-o-home',
    'balcony' => 'heroicon-o-sun',
    'breakfast' => 'heroicon-o-cake',
    'parking' => 'heroicon-o-truck',
    'pool' => 'heroicon-o-cloud',
    'gym' => 'heroicon-o-heart',
    'spa' => 'heroicon-o-sparkles',
    'restaurant' => 'heroicon-o-cake',
];

echo "Checking RoomAmenity records...\n";
$roomAmenities = RoomAmenity::all();
$fixedRoomCount = 0;

foreach ($roomAmenities as $amenity) {
    $originalIcon = $amenity->icon;
    
    // İkon null ise, varsayılan değer atayalım
    if ($amenity->icon === null) {
        $amenity->icon = 'heroicon-o-sparkles';
        $amenity->save();
        $fixedRoomCount++;
        echo "Fixed null icon for RoomAmenity ID {$amenity->id}: null -> heroicon-o-sparkles\n";
        continue;
    }
    
    // Eğer icon heroicon-o- ile başlamıyorsa, dönüşüm tablosuna bakalım
    if (!str_starts_with($amenity->icon, 'heroicon-o-')) {
        if (isset($iconMappings[$amenity->icon])) {
            $amenity->icon = $iconMappings[$amenity->icon];
            $amenity->save();
            $fixedRoomCount++;
            echo "Fixed known non-heroicon icon for RoomAmenity ID {$amenity->id}: {$originalIcon} -> {$amenity->icon}\n";
        } else {
            // Bilinen bir eşleşme yoksa, sparkles kullan
            $amenity->icon = 'heroicon-o-sparkles';
            $amenity->save();
            $fixedRoomCount++;
            echo "Fixed unknown icon for RoomAmenity ID {$amenity->id}: {$originalIcon} -> heroicon-o-sparkles\n";
        }
        continue;
    }
    
    // Eğer heroicon-o- ile başlıyor ama geçerli bir ikon değilse
    if (!in_array($amenity->icon, $validIcons)) {
        $amenity->icon = 'heroicon-o-sparkles';
        $amenity->save();
        $fixedRoomCount++;
        echo "Fixed invalid heroicon for RoomAmenity ID {$amenity->id}: {$originalIcon} -> heroicon-o-sparkles\n";
    }
}

// Aynı işlemleri HotelAmenity için yapalım
echo "\nChecking HotelAmenity records...\n";
try {
    $hotelAmenities = HotelAmenity::all();
    $fixedHotelCount = 0;

    foreach ($hotelAmenities as $amenity) {
        $originalIcon = $amenity->icon;
        
        // İkon null ise, varsayılan değer atayalım
        if ($amenity->icon === null) {
            $amenity->icon = 'heroicon-o-sparkles';
            $amenity->save();
            $fixedHotelCount++;
            echo "Fixed null icon for HotelAmenity ID {$amenity->id}: null -> heroicon-o-sparkles\n";
            continue;
        }
        
        // Eğer icon heroicon-o- ile başlamıyorsa, dönüşüm tablosuna bakalım
        if (!str_starts_with($amenity->icon, 'heroicon-o-')) {
            if (isset($iconMappings[$amenity->icon])) {
                $amenity->icon = $iconMappings[$amenity->icon];
                $amenity->save();
                $fixedHotelCount++;
                echo "Fixed known non-heroicon icon for HotelAmenity ID {$amenity->id}: {$originalIcon} -> {$amenity->icon}\n";
            } else {
                // Bilinen bir eşleşme yoksa, sparkles kullan
                $amenity->icon = 'heroicon-o-sparkles';
                $amenity->save();
                $fixedHotelCount++;
                echo "Fixed unknown icon for HotelAmenity ID {$amenity->id}: {$originalIcon} -> heroicon-o-sparkles\n";
            }
            continue;
        }
        
        // Eğer heroicon-o- ile başlıyor ama geçerli bir ikon değilse
        if (!in_array($amenity->icon, $validIcons)) {
            $amenity->icon = 'heroicon-o-sparkles';
            $amenity->save();
            $fixedHotelCount++;
            echo "Fixed invalid heroicon for HotelAmenity ID {$amenity->id}: {$originalIcon} -> heroicon-o-sparkles\n";
        }
    }
    
    echo "\nSummary:\n";
    echo "Fixed {$fixedRoomCount} RoomAmenity records\n";
    echo "Fixed {$fixedHotelCount} HotelAmenity records\n";
} catch (\Exception $e) {
    echo "Error processing HotelAmenity records: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";