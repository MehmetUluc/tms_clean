<?php

// Veritabanı bağlantısı ve migration sorununu düzeltmek için script

// Laravel uygulama örneğini başlat
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Migrations tablosuna bak
// Problem: Bu tablo var ama bazı migration'lar çalıştırılamamış
$db = $app->make('db');

// Core migration'ları tabloya ekle
$migrations = [
    '2025_05_01_000000_create_agencies_table',
    '2025_05_01_000100_create_hotels_table',
    '2025_05_01_000101_create_hotel_rooms_table',
    '2025_05_01_000102_create_hotel_boards_table',
    '2025_05_02_000100_create_rooms_tables',
    '2025_05_02_000100_create_bookings_tables',
    '2025_05_02_000101_create_guests_table',
    '2025_05_02_000200_create_promotions_table',
    '2025_05_03_000100_create_agency_tables',
    '2025_05_04_000100_create_transfer_tables',
];

// Bu migration'ları tabloya ekle, mevcut değillerse
$batch = $db->table('migrations')->max('batch') + 1;

foreach ($migrations as $migration) {
    // Migration tabloda var mı kontrol et
    $exists = $db->table('migrations')
        ->where('migration', $migration)
        ->exists();
    
    if (!$exists) {
        // Tabloya ekle
        $db->table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        echo "Migration '{$migration}' added to migrations table.\n";
    } else {
        echo "Migration '{$migration}' already exists in migrations table.\n";
    }
}

echo "Migrations table updated. All plugin migrations are now marked as completed.\n";