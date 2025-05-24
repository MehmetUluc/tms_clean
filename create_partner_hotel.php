<?php

use App\Plugins\Partner\Models\Partner;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\HotelType;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Partner'ı bul
$partner = Partner::where('onboarding_completed', true)->first();

if (!$partner) {
    echo "No completed partner found!\n";
    exit;
}

// Region oluştur veya bul
$region = Region::firstOrCreate([
    'name' => 'Antalya'
], [
    'type' => 'city',
    'parent_id' => null,
    'code' => '07',
    'is_active' => true,
    'sort_order' => 1,
]);

// HotelType oluştur veya bul
$hotelType = HotelType::firstOrCreate([
    'name' => 'Resort Hotel'
], [
    'slug' => 'resort-hotel',
    'description' => 'Tatil köyü',
    'is_active' => true,
    'sort_order' => 1,
]);

// Test oteli oluştur
$hotel = Hotel::create([
    'partner_id' => $partner->id,
    'region_id' => $region->id,
    'hotel_type_id' => $hotelType->id,
    'name' => 'Akdeniz Beach Resort',
    'slug' => 'akdeniz-beach-resort',
    'star_rating' => 5,
    'description' => 'Antalya\'nın en güzel sahilinde, muhteşem deniz manzaralı lüks tatil köyü.',
    'short_description' => 'Denize sıfır 5 yıldızlı tatil köyü',
    'email' => 'info@akdenizbeach.com',
    'phone' => '0242 555 12 34',
    'address' => 'Lara Sahili No:45',
    'latitude' => 36.8538,
    'longitude' => 30.8348,
    'check_in_time' => '14:00',
    'check_out_time' => '12:00',
    'total_rooms' => 250,
    'total_floors' => 7,
    'build_year' => 2018,
    'website' => 'www.akdenizbeach.com',
    'is_active' => true,
    'allow_refundable' => true,
    'allow_non_refundable' => true,
    'non_refundable_discount' => 15,
    'minimum_checkin_age' => 18,
    'payment_options' => json_encode(['credit_card', 'bank_transfer', 'cash']),
    'accepted_currencies' => json_encode(['TRY', 'EUR', 'USD']),
    'spoken_languages' => json_encode(['tr', 'en', 'de', 'ru']),
    'images' => json_encode([]),
    'facilities' => json_encode([
        'pool' => true,
        'spa' => true,
        'fitness' => true,
        'restaurant' => true,
        'bar' => true,
        'beach' => true,
        'parking' => true,
        'wifi' => true,
    ]),
]);

echo "Hotel created successfully!\n";
echo "Partner: {$partner->company_name}\n";
echo "Hotel: {$hotel->name}\n";
echo "Hotel ID: {$hotel->id}\n";