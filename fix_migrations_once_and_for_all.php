<?php

// Tüm migration sorunlarını tek seferde düzeltmek için kapsamlı bir script
// Bu script, mevcut durumu analiz eder ve sorunları çözer

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Schema\Blueprint;

echo "=== Kapsamlı Migration Düzeltme Aracı ===\n\n";

// 1. Migrations tablosunu kontrol et
if (!Schema::hasTable('migrations')) {
    echo "Migrations tablosu bulunamadı. Önce veritabanı bağlantınızı kontrol edin.\n";
    exit(1);
}

// 2. Duplike migration'ları tespit et ve düzelt
echo "Duplike migration kayıtlarını kontrol ediyorum...\n";

$duplicateMigrations = DB::table('migrations')
    ->select('migration')
    ->groupBy('migration')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if (count($duplicateMigrations) > 0) {
    echo "Duplike migration kayıtları bulundu:\n";
    
    foreach ($duplicateMigrations as $migration) {
        echo "- {$migration->migration}\n";
        
        // Duplike kayıtları sil, en son batch'i koru
        $latestRecord = DB::table('migrations')
            ->where('migration', $migration->migration)
            ->orderBy('batch', 'desc')
            ->first();
            
        DB::table('migrations')
            ->where('migration', $migration->migration)
            ->where('id', '!=', $latestRecord->id)
            ->delete();
            
        echo "  - Duplike kayıtlar silindi, en son batch korundu.\n";
    }
} else {
    echo "Duplike migration kaydı bulunamadı.\n";
}

// 3. Çakışan pricing migration'larını tespit et ve düzelt
echo "\nPricing migration çakışmalarını düzeltiyorum...\n";

// Ana dizindeki pricing migration'larını kontrol et
$corePricingMigrations = [
    '2025_05_04_100000_create_rate_plans_table',
    '2025_05_04_100001_create_daily_rates_table',
    '2025_05_04_100002_create_occupancy_rates_table',
    '2025_05_04_100003_create_child_policies_table',
    '2025_05_04_100004_create_inventories_table',
];

// Plugin dizinindeki yeni rate_plans migration'ını kontrol et
$pluginRatePlanMigration = '2025_05_07_062952_create_rate_plans_table';

// Pricing tablolarını kontrol et ve gerekirse düzelt
$pricingTables = ['rate_plans', 'daily_rates', 'occupancy_rates', 'child_policies', 'inventories'];

// Migration kayıtlarını düzelt
foreach ($corePricingMigrations as $migration) {
    $exists = DB::table('migrations')
        ->where('migration', $migration)
        ->exists();
        
    if ($exists) {
        echo "- Core pricing migration {$migration} kayıtlı.\n";
    } else {
        echo "- Core pricing migration {$migration} kayıtlı değil, ekleniyor...\n";
        $batch = DB::table('migrations')->max('batch') + 1;
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
    }
}

// Plugin migration'ı için özel işlem
$pluginMigrationExists = DB::table('migrations')
    ->where('migration', $pluginRatePlanMigration)
    ->exists();
    
if ($pluginMigrationExists) {
    echo "- Plugin rate_plans migration'ı zaten kayıtlı.\n";
} else {
    echo "- Plugin rate_plans migration'ı kayıtlı değil. Bu schema'nın farklı olduğunu not edelim.\n";
}

// 4. Tabloları kontrol et ve eksik tabloları oluştur veya yeniden oluştur
echo "\nTabloları kontrol ediyorum...\n";

$ratePlanTableExists = Schema::hasTable('rate_plans');

if (!$ratePlanTableExists) {
    echo "- rate_plans tablosu bulunamadı, oluşturuluyor...\n";
    
    // Bu tablo yoksa, plugin versiyonunu kullan
    $migrationFilePath = __DIR__ . '/app/Plugins/Pricing/database/migrations/' . $pluginRatePlanMigration . '.php';
    
    if (File::exists($migrationFilePath)) {
        $migration = include $migrationFilePath;
        $migration->up();
        echo "  - Plugin versiyonu ile rate_plans tablosu oluşturuldu.\n";
    } else {
        // Elle oluştur
        Schema::create('rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('board_type_id')->constrained('board_types')->onDelete('cascade');
            $table->boolean('is_per_person')->default(true)->comment('true: kişi bazlı, false: ünite bazlı');
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            // Her otel-oda-board_type için unique kombinasyon
            $table->unique(['hotel_id', 'room_id', 'board_type_id'], 'rate_plan_unique');
        });
        echo "  - rate_plans tablosu başarıyla oluşturuldu.\n";
    }
} else {
    echo "- rate_plans tablosu mevcut. Yapısını kontrol ediyorum...\n";
    
    // Şema farkını kontrol et
    $columns = Schema::getColumnListing('rate_plans');
    
    // Plugin schema'sı beklenen kolonlar
    $expectedColumns = ['id', 'hotel_id', 'room_id', 'board_type_id', 'is_per_person', 'status', 'created_at', 'updated_at'];
    $missingColumns = array_diff($expectedColumns, $columns);
    
    if (!empty($missingColumns)) {
        echo "  - rate_plans tablosunda eksik kolonlar var: " . implode(', ', $missingColumns) . "\n";
        echo "  - Tabloyu yeniden oluşturmak istiyorsanız, artisan migrate:fresh komutunu kullanın.\n";
    } else {
        echo "  - rate_plans tablosu doğru yapıda görünüyor.\n";
    }
}

// 5. Diğer pricing tablolarını kontrol et
foreach (['daily_rates', 'occupancy_rates', 'child_policies', 'inventories'] as $table) {
    if (!Schema::hasTable($table)) {
        echo "- {$table} tablosu bulunamadı. Bu tabloyu oluşturmak için migration'ları çalıştırmalısınız.\n";
    } else {
        echo "- {$table} tablosu mevcut.\n";
    }
}

// 6. Sessions tablosu çakışmasını düzelt
echo "\nSessions tablosu çakışmasını düzeltiyorum...\n";

$duplicateSessionsMigrations = [
    '2025_05_04_000014_create_sessions_table',
    '2025_05_05_000000_create_sessions_table'
];

// İlk migration'ı koru, diğerini sil
$firstSessionMigration = $duplicateSessionsMigrations[0];
$secondSessionMigration = $duplicateSessionsMigrations[1];

$firstExists = DB::table('migrations')
    ->where('migration', $firstSessionMigration)
    ->exists();
    
$secondExists = DB::table('migrations')
    ->where('migration', $secondSessionMigration)
    ->exists();
    
if ($firstExists && $secondExists) {
    DB::table('migrations')
        ->where('migration', $secondSessionMigration)
        ->delete();
    echo "- Duplike sessions migration kaydı silindi, {$firstSessionMigration} korundu.\n";
} elseif (!$firstExists && $secondExists) {
    echo "- Sadece {$secondSessionMigration} mevcut, bu yüzden korundu.\n";
} elseif ($firstExists && !$secondExists) {
    echo "- Sadece {$firstSessionMigration} mevcut, bu yüzden korundu.\n";
} else {
    echo "- Sessions migration'ları bulunamadı.\n";
}

// 7. Regions tablosunu kontrol et
echo "\nRegions tablosunu kontrol ediyorum...\n";

if (Schema::hasTable('regions')) {
    echo "- regions tablosu mevcut. Gerekli kolonlar kontrol ediliyor...\n";
    
    // Gerekli kolonları kontrol et
    $columnsToCheck = [
        'parent_id', 'type', 'code', 'latitude', 'longitude', 
        'timezone', 'sort_order', 'is_featured'
    ];
    
    $regionColumns = Schema::getColumnListing('regions');
    $missingColumns = array_diff($columnsToCheck, $regionColumns);
    
    if (!empty($missingColumns)) {
        echo "  - Eksik kolonlar bulundu: " . implode(', ', $missingColumns) . "\n";
        echo "  - fix_regions_table.php scriptini çalıştırın veya migration'ı tekrar çalıştırın.\n";
    } else {
        echo "  - regions tablosu doğru yapıda görünüyor.\n";
    }
} else {
    echo "- regions tablosu bulunamadı. Önce migration'ları çalıştırmalısınız.\n";
}

// 8. Null değerli sort_order'ları düzelt
echo "\nNull sort_order değerlerini düzeltiyorum...\n";

$tablesWithSortOrder = ['room_amenities', 'hotel_amenities'];

foreach ($tablesWithSortOrder as $table) {
    if (Schema::hasTable($table) && Schema::hasColumn($table, 'sort_order')) {
        $nullCount = DB::table($table)
            ->whereNull('sort_order')
            ->count();
            
        if ($nullCount > 0) {
            DB::table($table)
                ->whereNull('sort_order')
                ->update(['sort_order' => 0]);
                
            echo "- {$table} tablosunda {$nullCount} null sort_order değeri 0 olarak güncellendi.\n";
        } else {
            echo "- {$table} tablosunda null sort_order değeri bulunamadı.\n";
        }
    } else {
        echo "- {$table} tablosu veya sort_order kolonu bulunamadı.\n";
    }
}

// 9. JSON sütunlarını kontrol et ve düzelt
echo "\nJSON sütunlarını kontrol ediyorum...\n";

if (Schema::hasTable('hotels')) {
    $jsonColumns = ['amenities', 'policies', 'gallery', 'check_in_out'];
    $jsonColumnsToFix = [];
    
    foreach ($jsonColumns as $column) {
        if (Schema::hasColumn('hotels', $column)) {
            $jsonColumnsToFix[] = $column;
        }
    }
    
    if (!empty($jsonColumnsToFix)) {
        echo "- hotels tablosunda JSON sütunları düzeltiliyor...\n";
        
        foreach ($jsonColumnsToFix as $column) {
            try {
                DB::statement("ALTER TABLE hotels MODIFY {$column} JSON NULL DEFAULT (JSON_ARRAY())");
                echo "  - {$column} sütunu başarıyla düzeltildi.\n";
            } catch (\Exception $e) {
                echo "  - {$column} sütunu düzeltilemedi: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "- hotels tablosunda düzeltilecek JSON sütunu bulunamadı.\n";
    }
} else {
    echo "- hotels tablosu bulunamadı.\n";
}

// 10. ThemeManager migrations çakışmasını düzelt
echo "\nThemeManager migrations çakışmasını düzeltiyorum...\n";

$themeManagerMigrations = [
    '2025_05_05_000000_create_theme_settings_table',
    '2025_05_05_140000_recreate_theme_settings_table',
    '2025_05_05_140000_update_theme_settings_table'
];

$themeManagerMigrationStatuses = [];

foreach ($themeManagerMigrations as $migration) {
    $exists = DB::table('migrations')
        ->where('migration', $migration)
        ->exists();
        
    $themeManagerMigrationStatuses[$migration] = $exists;
}

echo "- ThemeManager migration durumları:\n";
foreach ($themeManagerMigrationStatuses as $migration => $exists) {
    echo "  - {$migration}: " . ($exists ? "Kayıtlı" : "Kayıtlı değil") . "\n";
}

// Son iki migration aynı timestamp'e sahip - bu sorunu düzelt
if ($themeManagerMigrationStatuses[$themeManagerMigrations[1]] && 
    $themeManagerMigrationStatuses[$themeManagerMigrations[2]]) {
    
    // Son migration'ı farklı bir timestamp'e taşı
    $oldMigration = $themeManagerMigrations[2];
    $newMigration = str_replace('140000', '140001', $oldMigration);
    
    DB::table('migrations')
        ->where('migration', $oldMigration)
        ->update(['migration' => $newMigration]);
        
    echo "- Çakışan timestamp düzeltildi: {$oldMigration} -> {$newMigration}\n";
}

// 11. Özet durum raporu
echo "\n=== Migration Sistemi Durum Raporu ===\n";

$totalMigrations = DB::table('migrations')->count();
echo "Toplam kayıtlı migration sayısı: {$totalMigrations}\n";

$lastBatch = DB::table('migrations')->max('batch');
echo "Son migration batch numarası: {$lastBatch}\n";

echo "\nÖnemli tablolar kontrol durumu:\n";
$criticalTables = [
    'users', 'regions', 'hotels', 'rooms', 'reservations', 
    'rate_plans', 'daily_rates', 'occupancy_rates', 'child_policies', 'inventories'
];

foreach ($criticalTables as $table) {
    echo "- {$table}: " . (Schema::hasTable($table) ? "✓" : "✗") . "\n";
}

echo "\n=== Sonuç ===\n";
echo "Migration düzeltme işlemi tamamlandı. Artisan komutları şimdi daha güvenli bir şekilde çalıştırılabilir.\n";
echo "Önerilen adımlar:\n";
echo "1. Veritabanı yedeklerini alın\n";
echo "2. php artisan migrate:status komutunu çalıştırarak durumu kontrol edin\n";
echo "3. Çalıştırılmamış migration'lar için php artisan migrate komutunu çalıştırın\n";
echo "4. Hala sorun yaşıyorsanız, veritabanı yapısını tamamen yenilemek için php artisan migrate:fresh --seed komutunu kullanabilirsiniz (DİKKAT: Tüm veriyi siler!)\n";