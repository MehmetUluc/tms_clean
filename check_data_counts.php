<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TMS Veri Sayıları ===\n\n";

$tables = ['regions', 'hotel_types', 'hotel_tags', 'board_types', 'hotels', 'room_types', 'room_amenities', 'hotel_amenities', 'rooms', 'reservations', 'guests'];

foreach ($tables as $table) {
    $count = DB::table($table)->count();
    echo "{$table}: {$count} kayıt\n";
}

// Get sample data to confirm contents
$hotelSample = DB::table('hotels')->first();
echo "\nÖrnek Otel: " . json_encode($hotelSample, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

$roomSample = DB::table('rooms')->first();
echo "\nÖrnek Oda: " . json_encode($roomSample, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

$reservationSample = DB::table('reservations')->first();
echo "\nÖrnek Rezervasyon: " . json_encode($reservationSample, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";