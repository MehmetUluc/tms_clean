<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "=== TMS Cache Tablosu Oluşturma Aracı ===\n\n";

try {
    // Cache tablosunu oluştur
    if (!Schema::hasTable('cache')) {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });
        echo "Cache tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Cache tablosu zaten mevcut.\n";
    }
    
    // Cache locks tablosunu oluştur
    if (!Schema::hasTable('cache_locks')) {
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
        echo "Cache Locks tablosu başarıyla oluşturuldu.\n";
    } else {
        echo "Cache Locks tablosu zaten mevcut.\n";
    }
    
    // Cache table için migration kaydı ekle
    if (Schema::hasTable('migrations')) {
        if (!DB::table('migrations')->where('migration', 'create_cache_tables')->exists()) {
            DB::table('migrations')->insert([
                'migration' => 'create_cache_tables',
                'batch' => DB::table('migrations')->max('batch') + 1,
            ]);
            echo "Cache tablolarının migration kaydı oluşturuldu.\n";
        }
    }
    
    echo "\nTüm cache tabloları başarıyla oluşturuldu.\n";
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
}