<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

echo "=== Oda Ekleme Debug ===\n\n";

try {
    // Get the first hotel
    $hotel = DB::table('hotels')->first();
    echo "Otel: {$hotel->name} (ID: {$hotel->id})\n\n";
    
    // Get the first room type
    $roomType = DB::table('room_types')->first();
    echo "Oda Tipi: {$roomType->name} (ID: {$roomType->id})\n\n";
    
    // Get columns from rooms table
    echo "Odalar tablosu sütunları:\n";
    $columns = Schema::getColumnListing('rooms');
    print_r($columns);
    
    // Trying to add a room
    echo "\nOda eklenmeye çalışılıyor...\n";
    
    $roomId = DB::table('rooms')->insertGetId([
        'hotel_id' => $hotel->id,
        'room_type_id' => $roomType->id,
        'name' => $roomType->name . ' Test',
        'room_number' => '101',
        'floor' => 1,
        'description' => $roomType->name . ' tipi oda, ' . $roomType->max_adults . ' yetişkin ve ' . $roomType->max_children . ' çocuk kapasiteli',
        'notes' => 'Oda ' . $roomType->size . ' metrekare büyüklüğünde',
        'is_active' => true,
        'status' => 'available',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Oda eklendi, ID: {$roomId}\n";
    
    // Get board types
    $boardType = DB::table('board_types')->first();
    echo "\nPansiyon Tipi: {$boardType->name} (ID: {$boardType->id})\n\n";
    
    // Add room-board type relationship
    DB::table('room_board_type')->insert([
        'room_id' => $roomId,
        'board_type_id' => $boardType->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Oda-Pansiyon ilişkisi eklendi\n";
    
} catch (Exception $e) {
    echo "HATA: " . $e->getMessage() . "\n";
    echo "Satır: " . $e->getLine() . "\n";
    echo "Dosya: " . $e->getFile() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}