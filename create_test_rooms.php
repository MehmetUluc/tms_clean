<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

echo "=== Oda ve Rezervasyon Test Oluşturucu ===\n\n";

// Enable more detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Clean existing data
    echo "Mevcut test verileri temizleniyor...\n";
    DB::table('guests')->delete();
    DB::table('reservations')->delete();
    DB::table('rate_plans')->delete();
    DB::table('room_board_type')->delete();
    DB::table('room_room_amenity')->delete();
    DB::table('rooms')->delete();
    
    // Get first hotel
    $hotel = DB::table('hotels')->first();
    if (!$hotel) {
        throw new Exception("Hiç otel bulunamadı.");
    }
    
    // Get room types
    $roomTypes = DB::table('room_types')->get();
    if ($roomTypes->isEmpty()) {
        throw new Exception("Hiç oda tipi bulunamadı.");
    }
    
    // Get board types
    $boardTypes = DB::table('board_types')->get();
    if ($boardTypes->isEmpty()) {
        throw new Exception("Hiç pansiyon tipi bulunamadı.");
    }
    
    // Get room amenities
    $amenities = DB::table('room_amenities')->pluck('id')->toArray();
    if (empty($amenities)) {
        throw new Exception("Hiç oda özelliği bulunamadı.");
    }
    
    echo "Hotel ID: {$hotel->id}, Name: {$hotel->name}\n";
    
    foreach ($roomTypes as $roomType) {
        echo "\nOda tipi: {$roomType->name} (ID: {$roomType->id})\n";
        
        // Create 2 rooms for each type
        for ($i = 1; $i <= 2; $i++) {
            echo "  Oda {$i} oluşturuluyor...\n";
            
            $roomId = DB::table('rooms')->insertGetId([
                'hotel_id' => $hotel->id,
                'room_type_id' => $roomType->id,
                'name' => $roomType->name . ' ' . $i,
                'room_number' => $roomType->id . '0' . $i,
                'floor' => 1,
                'description' => $roomType->name . ' tipi oda, ' . $roomType->max_adults . ' yetişkin kapasiteli',
                'notes' => 'Oda ' . $roomType->size . ' metrekare büyüklüğünde',
                'is_active' => true,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            echo "    Oda oluşturuldu (ID: {$roomId})\n";
            
            // Add room amenities
            shuffle($amenities);
            $roomAmenities = array_slice($amenities, 0, 3);
            foreach ($roomAmenities as $amenityId) {
                DB::table('room_room_amenity')->insert([
                    'room_id' => $roomId,
                    'room_amenity_id' => $amenityId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            echo "    Oda özellikleri eklendi\n";
            
            // Add board types
            foreach ($boardTypes as $boardType) {
                DB::table('room_board_type')->insert([
                    'room_id' => $roomId,
                    'board_type_id' => $boardType->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Create rate plan
                DB::table('rate_plans')->insert([
                    'hotel_id' => $hotel->id,
                    'room_id' => $roomId,
                    'board_type_id' => $boardType->id,
                    'is_per_person' => true,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            echo "    Pansiyon tipleri ve rate planları eklendi\n";
            
            // Create a reservation for the first room of each type
            if ($i == 1) {
                $checkInDate = now()->addDays(rand(5, 15));
                $nights = rand(2, 7);
                $checkOutDate = (clone $checkInDate)->addDays($nights);
                $boardTypeId = $boardTypes->random()->id;
                
                $reservationId = DB::table('reservations')->insertGetId([
                    'reservation_number' => 'RES-' . rand(10000, 99999),
                    'hotel_id' => $hotel->id,
                    'room_id' => $roomId,
                    'board_type_id' => $boardTypeId,
                    'check_in_date' => $checkInDate->format('Y-m-d'),
                    'check_out_date' => $checkOutDate->format('Y-m-d'),
                    'check_in_time' => '14:00:00',
                    'check_out_time' => '12:00:00',
                    'adults' => rand(1, (int)$roomType->max_adults),
                    'children' => rand(0, (int)$roomType->max_children),
                    'infants' => 0,
                    'total_price' => rand(1000, 5000),
                    'currency' => 'TRY',
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'source' => 'online',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "    Rezervasyon oluşturuldu (ID: {$reservationId})\n";
                
                // Add guest
                DB::table('guests')->insert([
                    'reservation_id' => $reservationId,
                    'first_name' => 'Test',
                    'last_name' => 'Misafir' . rand(1, 100),
                    'email' => 'guest' . rand(1, 100) . '@example.com',
                    'phone' => '+90 555 ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'is_primary' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "    Misafir eklendi\n";
            }
        }
    }
    
    echo "\nİşlem tamamlandı! Kontrol ediliyor...\n";
    $roomCount = DB::table('rooms')->count();
    $reservationCount = DB::table('reservations')->count();
    $guestCount = DB::table('guests')->count();
    
    echo "Oluşturulan oda sayısı: {$roomCount}\n";
    echo "Oluşturulan rezervasyon sayısı: {$reservationCount}\n";
    echo "Oluşturulan misafir sayısı: {$guestCount}\n";
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
    echo "Satır: " . $e->getLine() . "\n";
    echo "Dosya: " . $e->getFile() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}