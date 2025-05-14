<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== TMS Migration Tablosu Onarım Aracı ===\n\n";

try {
    // Migrations tablosunu oluştur
    if (!Schema::hasTable('migrations')) {
        Schema::create('migrations', function ($table) {
            $table->increments('id');
            $table->string('migration');
            $table->integer('batch');
        });
        echo "Migrations tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Migrations tablosu zaten mevcut.\n";
    }
    
    echo "\nŞimdi migration'ları çalıştırabilirsiniz:\n";
    echo "php artisan migrate\n";
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}