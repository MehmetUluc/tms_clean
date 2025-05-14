<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FixMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix 
                            {--fresh : Drop all tables and run migrations from scratch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix migration issues and ensure proper migration paths';

    /**
     * Migration paths to check
     * 
     * @var array
     */
    protected $migrationPaths = [];

    /**
     * Problematic migrations to handle specially
     * 
     * @var array
     */
    protected $problemMigrations = [
        // Duplicate rate plans migrations
        '2025_05_04_100000_create_rate_plans_table' => [
            'core' => 'database/migrations',
            'plugin' => 'app/Plugins/Pricing/database/migrations'
        ],
        '2025_05_07_062952_create_rate_plans_table' => [
            'plugin' => 'app/Plugins/Pricing/database/migrations'
        ],
        
        // Duplicate sessions migrations
        '2025_05_04_000014_create_sessions_table' => [
            'core' => 'database/migrations'
        ],
        '2025_05_05_000000_create_sessions_table' => [
            'core' => 'database/migrations'
        ],
        
        // Pricing migrations that could be in both places
        '2025_05_04_100001_create_daily_rates_table' => [
            'core' => 'database/migrations',
            'plugin' => 'app/Plugins/Pricing/database/migrations'
        ],
        '2025_05_04_100002_create_occupancy_rates_table' => [
            'core' => 'database/migrations',
            'plugin' => 'app/Plugins/Pricing/database/migrations'
        ],
        '2025_05_04_100003_create_child_policies_table' => [
            'core' => 'database/migrations',
            'plugin' => 'app/Plugins/Pricing/database/migrations'
        ],
        '2025_05_04_100004_create_inventories_table' => [
            'core' => 'database/migrations',
            'plugin' => 'app/Plugins/Pricing/database/migrations'
        ],
        
        // Theme manager migrations with same timestamp
        '2025_05_05_140000_recreate_theme_settings_table' => [
            'plugin' => 'app/Plugins/ThemeManager/database/migrations'
        ],
        '2025_05_05_140000_update_theme_settings_table' => [
            'plugin' => 'app/Plugins/ThemeManager/database/migrations'
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('TMS Migration Sorunlarını Çözme Aracı');
        $this->info('========================================');
        
        // Migrations tablosunu kontrol et
        if (!Schema::hasTable('migrations')) {
            $this->error('Migrations tablosu bulunamadı. Veritabanını kontrol edin.');
            return 1;
        }
        
        // Migration yollarını bulalım
        $this->discoverMigrationPaths();
        
        // --fresh seçeneği kullanıldıysa
        if ($this->option('fresh')) {
            if ($this->confirm('Bu işlem tüm tabloları silecek ve sıfırdan oluşturacak. Devam etmek istiyor musunuz?', false)) {
                $this->info('Tabloları temizleme işlemi başlıyor...');
                $this->call('migrate:fresh');
                $this->info('İşlem tamamlandı.');
                return 0;
            }
            
            $this->info('İşlem iptal edildi.');
            return 0;
        }
        
        // Ana çözüm adımları
        $this->fixDuplicateMigrations();
        $this->fixConflictingTimestamps();
        $this->registerMissingMigrations();
        $this->info('Migration sorunları giderildi.');
        
        // PHP artisan migrate komutunu çağır
        if ($this->confirm('Şimdi php artisan migrate çalıştırmak ister misiniz?', true)) {
            $this->call('migrate');
        }
        
        $this->info('İşlem tamamlandı.');
        return 0;
    }
    
    /**
     * Migration yollarını tespit et
     */
    protected function discoverMigrationPaths()
    {
        $this->info('Migration yolları tespit ediliyor...');
        
        // Ana migrations klasörü
        $this->migrationPaths[] = database_path('migrations');
        
        // Plugin migrations klasörleri
        $pluginsDir = app_path('Plugins');
        if (File::exists($pluginsDir)) {
            foreach (File::directories($pluginsDir) as $pluginDir) {
                $migrationPath = $pluginDir . '/database/migrations';
                if (File::exists($migrationPath)) {
                    $this->migrationPaths[] = $migrationPath;
                }
            }
        }
        
        $this->info(count($this->migrationPaths) . ' migration yolu bulundu.');
    }
    
    /**
     * Duplike migration'ları çöz
     */
    protected function fixDuplicateMigrations()
    {
        $this->info('Duplike migration kayıtları düzeltiliyor...');
        
        // Migrations tablosundan tüm kayıtlar
        $migrations = DB::table('migrations')->get();
        
        // Başlangıçta aynı isimli kayıtları bul
        $counts = [];
        foreach ($migrations as $migration) {
            $counts[$migration->migration] = ($counts[$migration->migration] ?? 0) + 1;
        }
        
        // Duplike olanları işle
        $duplicates = array_filter($counts, function($count) {
            return $count > 1;
        });
        
        if (count($duplicates) > 0) {
            $this->info(count($duplicates) . ' duplike migration bulundu.');
            
            foreach ($duplicates as $migration => $count) {
                $this->warn("  - $migration (${count}x)");
                
                // En yüksek batch numarasına sahip olanı koru
                $highestBatch = DB::table('migrations')
                    ->where('migration', $migration)
                    ->orderBy('batch', 'desc')
                    ->first();
                
                // Diğerlerini sil
                $deleted = DB::table('migrations')
                    ->where('migration', $migration)
                    ->where('id', '<>', $highestBatch->id)
                    ->delete();
                
                $this->info("    - Duplications removed, keeping batch {$highestBatch->batch}");
            }
        } else {
            $this->info('Duplike migration kaydı bulunmadı.');
        }
    }
    
    /**
     * Çakışan timestamp'leri düzelt
     */
    protected function fixConflictingTimestamps()
    {
        $this->info('Çakışan timestamp\'ler düzeltiliyor...');
        
        // Aynı timestamp'e sahip olan ThemeManager migration'larını kontrol et
        $themeMigration1 = '2025_05_05_140000_recreate_theme_settings_table';
        $themeMigration2 = '2025_05_05_140000_update_theme_settings_table';
        
        $exists1 = DB::table('migrations')->where('migration', $themeMigration1)->exists();
        $exists2 = DB::table('migrations')->where('migration', $themeMigration2)->exists();
        
        if ($exists1 && $exists2) {
            $this->warn("Çakışan timestamp'ler bulundu:");
            $this->warn("  - $themeMigration1");
            $this->warn("  - $themeMigration2");
            
            // İkinci migration'ın timestamp'ini bir saniye sonrası ile değiştir
            $newMigrationName = '2025_05_05_140001_update_theme_settings_table';
            
            DB::table('migrations')
                ->where('migration', $themeMigration2)
                ->update(['migration' => $newMigrationName]);
                
            $this->info("  - Migration ismi güncellendi: $themeMigration2 -> $newMigrationName");
            
            // Dosya adını da değiştir (eğer mevcutsa)
            $migrationPath = null;
            foreach ($this->migrationPaths as $path) {
                $filePath = $path . '/' . $themeMigration2 . '.php';
                if (File::exists($filePath)) {
                    $migrationPath = $filePath;
                    break;
                }
            }
            
            if ($migrationPath) {
                $newPath = dirname($migrationPath) . '/' . $newMigrationName . '.php';
                File::move($migrationPath, $newPath);
                $this->info("  - Migration dosyası yeniden adlandırıldı.");
            }
        } else {
            $this->info('Çakışan timestamp bulunamadı.');
        }
    }
    
    /**
     * Eksik migration kayıtlarını ekle
     */
    protected function registerMissingMigrations()
    {
        $this->info('Eksik migration kayıtları kontrol ediliyor...');
        
        // Migrations tablosunda kayıtlı tüm migration isimleri
        $existingMigrations = DB::table('migrations')->pluck('migration')->toArray();
        
        // Tüm yollardaki migration dosyalarını tara
        $migrationFiles = [];
        
        foreach ($this->migrationPaths as $path) {
            $files = File::glob($path . '/*.php');
            
            foreach ($files as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $migrationFiles[$filename] = $file;
            }
        }
        
        // Eksik olanları belirle
        $missingMigrations = array_diff(array_keys($migrationFiles), $existingMigrations);
        
        if (count($missingMigrations) > 0) {
            $this->info(count($missingMigrations) . ' eksik migration bulundu:');
            
            // Son batch numarasını al
            $batch = DB::table('migrations')->max('batch') + 1;
            
            foreach ($missingMigrations as $migration) {
                // Özel durumlar için kontrol et
                if (array_key_exists($migration, $this->problemMigrations)) {
                    $this->warn("  - $migration (Özel migration - kontrol edilmeli)");
                    
                    // Tabloyu zaten oluşturan bir migration kayıtlı mı?
                    if (Str::contains($migration, 'create_rate_plans_table')) {
                        $tableName = 'rate_plans';
                        $otherRatePlanMigrations = array_filter($existingMigrations, function($m) {
                            return Str::contains($m, 'create_rate_plans_table');
                        });
                        
                        if (!empty($otherRatePlanMigrations)) {
                            $this->info("    - Zaten bir rate_plans migration'ı kayıtlı, bu atlanıyor.");
                            continue;
                        }
                    }
                    
                    if (Str::contains($migration, 'create_sessions_table')) {
                        $otherSessionMigrations = array_filter($existingMigrations, function($m) {
                            return Str::contains($m, 'create_sessions_table');
                        });
                        
                        if (!empty($otherSessionMigrations)) {
                            $this->info("    - Zaten bir sessions migration'ı kayıtlı, bu atlanıyor.");
                            continue;
                        }
                    }
                }
                
                // Migrations tablosuna ekle
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch
                ]);
                
                $this->info("  - $migration eklendi (batch: $batch)");
            }
        } else {
            $this->info('Tüm migration\'lar kayıtlı görünüyor.');
        }
    }
}