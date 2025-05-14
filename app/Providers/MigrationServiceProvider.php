<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Str;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Ana migrations klasörü her zaman yüklenir
        
        // Plugin migrations klasörlerini yükle
        $this->loadPluginMigrations();
        
        // Komutları ekle
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\FixMigrationsCommand::class,
            ]);
        }
    }
    
    /**
     * Plugin migrations klasörlerini bulup yükle
     */
    protected function loadPluginMigrations(): void
    {
        $pluginsDir = app_path('Plugins');
        
        if (!File::exists($pluginsDir)) {
            return;
        }
        
        // Öncelikle, hangi migration yollarının çakışabileceğini görelim
        $conflictPaths = [];
        $knownMigrations = [];
        
        // Tüm plugin migration yollarını tara
        foreach (File::directories($pluginsDir) as $pluginDir) {
            $migrationPath = $pluginDir . '/database/migrations';
            
            if (File::exists($migrationPath)) {
                // Bu plugin'in tüm migration dosyalarını tara
                $files = File::glob($migrationPath . '/*.php');
                
                // Her migration dosyası için ne oluşturduğunu kontrol et
                foreach ($files as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    
                    // Eğer bu migration bir tablo oluşturuyorsa, bunu işaretle
                    if (Str::contains($filename, 'create_') && Str::contains($filename, '_table')) {
                        $tableName = $this->extractTableName($filename);
                        
                        if (!empty($tableName)) {
                            // Bu tablo için başka bir migration var mı?
                            if (isset($knownMigrations[$tableName])) {
                                $conflictPaths[$tableName][] = $migrationPath;
                            } else {
                                $knownMigrations[$tableName] = $migrationPath;
                                $conflictPaths[$tableName] = [$migrationPath];
                            }
                        }
                    }
                }
                
                // Migration yolunu Laravel'e kaydet
                $this->loadMigrationsFrom($migrationPath);
            }
        }
        
        // Çakışan migration yollarını logla
        foreach ($conflictPaths as $table => $paths) {
            if (count($paths) > 1) {
                logger()->warning("Migration conflict detected for table '$table' in paths: " . implode(', ', $paths));
            }
        }
    }
    
    /**
     * Migration dosya adından tablo adını çıkar
     */
    protected function extractTableName(string $filename): string
    {
        // create_users_table -> users
        // create_rate_plans_table -> rate_plans
        preg_match('/create_(.+?)_table/', $filename, $matches);
        
        return $matches[1] ?? '';
    }
}