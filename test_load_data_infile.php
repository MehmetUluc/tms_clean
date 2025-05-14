<?php

require __DIR__ . '/vendor/autoload.php';

// Laravel bootstrap
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

echo "LOAD DATA INFILE Performance Test\n";
echo "--------------------------------\n\n";

// Test için günlük fiyat tablosunu oluştur (eğer yoksa)
if (!Schema::hasTable('test_daily_rates')) {
    echo "Creating test table...\n";
    Schema::create('test_daily_rates', function ($table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('rate_plan_id');
        $table->date('date');
        $table->decimal('price', 10, 2);
        $table->timestamps();
        
        $table->unique(['rate_plan_id', 'date']);
    });
    echo "Test table created.\n\n";
}

// Test parametreleri
$ratePlanId = 123; // Test için rate plan id
$totalRecords = 10000; // 10,000 kayıt (bellek sınırlaması nedeniyle azaltıldı)
$startDate = Carbon::parse('2024-01-01');

echo "Generating {$totalRecords} test records...\n";

// Test verilerini oluştur
$prices = [];
for ($i = 0; $i < $totalRecords; $i++) {
    $date = $startDate->copy()->addDays($i)->format('Y-m-d');
    $prices[$date] = mt_rand(100, 1000); // Rastgele fiyat
}

echo "Test data generated.\n\n";

// Test fonksiyonu: LOAD DATA INFILE
function importWithLoadDataInfile($ratePlanId, $prices)
{
    // Geçici CSV dosyası oluştur
    $tempFile = tempnam(sys_get_temp_dir(), 'price_import_');
    $file = fopen($tempFile, 'w');
    
    // CSV içeriğini hazırla
    $now = Carbon::now()->format('Y-m-d H:i:s');
    foreach ($prices as $date => $price) {
        // CSV formatı: rate_plan_id, date, price, created_at, updated_at
        fprintf($file, "%d,%s,%.2f,%s,%s\n", 
            $ratePlanId, $date, $price, $now, $now);
    }
    
    fclose($file);
    
    // Dosya boyutunu göster
    $fileSize = filesize($tempFile) / 1024; // KB cinsinden
    echo "CSV file created: " . number_format($fileSize, 2) . " KB\n";
    
    // MySQL'e LOAD DATA INFILE için PDO ayarlarını yapılandır
    $pdo = DB::connection()->getPdo();
    $pdo->setAttribute(\PDO::MYSQL_ATTR_LOCAL_INFILE, true);
    
    // Önce tabloyu temizle
    DB::table('test_daily_rates')->where('rate_plan_id', $ratePlanId)->delete();
    echo "Table cleared for test.\n";
    
    // LOAD DATA INFILE komutunu hazırla
    $query = "
        LOAD DATA LOCAL INFILE '{$tempFile}'
        INTO TABLE test_daily_rates
        FIELDS TERMINATED BY ','
        LINES TERMINATED BY '\\n'
        (rate_plan_id, date, price, created_at, updated_at)
    ";
    
    // Başlangıç zamanını kaydet
    $startTime = microtime(true);
    
    try {
        // Komutu çalıştır
        $result = DB::unprepared($query);
        
        // Bitiş zamanını kaydet
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        echo "LOAD DATA INFILE executed successfully.\n";
        echo "Time taken: " . number_format($duration, 4) . " seconds\n";
        echo "Records per second: " . number_format(count($prices) / $duration, 2) . "\n";
        
        // Eklenen kayıt sayısını kontrol et
        $insertedCount = DB::table('test_daily_rates')
            ->where('rate_plan_id', $ratePlanId)
            ->count();
        
        echo "Verified record count: {$insertedCount}\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    // Geçici dosyayı temizle
    unlink($tempFile);
    echo "Temporary file removed.\n";
}

// Karşılaştırma için: Standart Laravel Bulk Insert
function importWithBulkInsert($ratePlanId, $prices)
{
    // Önce tabloyu temizle
    DB::table('test_daily_rates')->where('rate_plan_id', $ratePlanId)->delete();
    echo "Table cleared for test.\n";

    // Başlangıç zamanını kaydet
    $startTime = microtime(true);

    try {
        // Daha az bellek kullanan yöntem - diziyi daha küçük parçalara böl
        $chunks = array_chunk($prices, 500, true);
        $totalInserted = 0;

        foreach ($chunks as $chunk) {
            $insertData = [];
            $now = Carbon::now();

            foreach ($chunk as $date => $price) {
                $insertData[] = [
                    'rate_plan_id' => $ratePlanId,
                    'date' => $date,
                    'price' => $price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $inserted = DB::table('test_daily_rates')->insert($insertData);
            $totalInserted += count($chunk);
        }

        // Bitiş zamanını kaydet
        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        echo "Bulk Insert completed successfully.\n";
        echo "Time taken: " . number_format($duration, 4) . " seconds\n";
        echo "Records per second: " . number_format(count($prices) / $duration, 2) . "\n";

        // Eklenen kayıt sayısını kontrol et
        $insertedCount = DB::table('test_daily_rates')
            ->where('rate_plan_id', $ratePlanId)
            ->count();

        echo "Verified record count: {$insertedCount}\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// Karşılaştırma için: UPSERT (tek tek)
function importWithUpsert($ratePlanId, $prices) 
{
    // Önce tabloyu temizle
    DB::table('test_daily_rates')->where('rate_plan_id', $ratePlanId)->delete();
    echo "Table cleared for test.\n";
    
    // Başlangıç zamanını kaydet
    $startTime = microtime(true);
    
    // UPSERT fonksiyonuyla kaydet (sadece 1000 kayıt test ediyoruz, çok uzun sürecek)
    $testPrices = array_slice($prices, 0, 1000, true);
    
    try {
        $count = 0;
        foreach ($testPrices as $date => $price) {
            DB::table('test_daily_rates')->updateOrInsert(
                ['rate_plan_id' => $ratePlanId, 'date' => $date],
                [
                    'price' => $price, 
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
            $count++;
        }
        
        // Bitiş zamanını kaydet
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        echo "UPSERT completed successfully (1000 records).\n";
        echo "Time taken: " . number_format($duration, 4) . " seconds\n";
        echo "Records per second: " . number_format($count / $duration, 2) . "\n";
        echo "Estimated time for {$totalRecords} records: " . 
             number_format(($duration / $count) * $totalRecords, 2) . " seconds\n";
        
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

// 1) LOAD DATA INFILE ile test
echo "\n=== TEST 1: LOAD DATA INFILE ===\n";
importWithLoadDataInfile($ratePlanId, $prices);

// 2) Bulk Insert ile test
echo "\n=== TEST 2: BULK INSERT ===\n";
importWithBulkInsert($ratePlanId, $prices);

// 3) UPSERT ile test (sadece 1000 kayıt, çok uzun sürecek)
echo "\n=== TEST 3: UPSERT (1000 records only) ===\n";
importWithUpsert($ratePlanId, $prices);

// Test tablosunu temizle
echo "\nCleaning up test data...\n";
DB::table('test_daily_rates')->where('rate_plan_id', $ratePlanId)->delete();
echo "Test completed.\n";