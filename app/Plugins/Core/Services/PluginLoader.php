<?php

namespace App\Plugins\Core\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Filament\Contracts\Plugin;

class PluginLoader
{
    /**
     * Tüm plugin'leri otomatik olarak yükle
     */
    public function loadAllPlugins(): array
    {
        $plugins = [];
        $pluginsDir = config('core.plugins_directory', app_path('Plugins'));
        
        // Plugins dizini var mı kontrol et
        if (!File::isDirectory($pluginsDir)) {
            return $plugins;
        }
        
        // Tüm plugin dizinlerini oku
        $dirs = File::directories($pluginsDir);
        
        foreach ($dirs as $dir) {
            $name = basename($dir);
            
            // Core plugin'i atla - bu özel bir plugin
            if ($name === 'Core') {
                continue;
            }
            
            // Plugin sınıfını oluştur
            $className = "\\App\\Plugins\\{$name}\\{$name}Plugin";
            
            if (class_exists($className)) {
                try {
                    $plugin = $className::make();
                    
                    if ($plugin instanceof Plugin) {
                        $plugins[$plugin->getId()] = $plugin;
                        Log::info("Plugin yüklendi: {$name}");
                    }
                } catch (\Exception $e) {
                    Log::error("Plugin yüklenemedi: {$name}", ['error' => $e->getMessage()]);
                }
            }
        }
        
        return $plugins;
    }
    
    /**
     * Bir plugin'i adına göre yükle
     */
    public function loadPlugin(string $name): ?Plugin
    {
        $className = "\\App\\Plugins\\{$name}\\{$name}Plugin";
        
        if (class_exists($className)) {
            try {
                $plugin = $className::make();
                
                if ($plugin instanceof Plugin) {
                    return $plugin;
                }
            } catch (\Exception $e) {
                Log::error("Plugin yüklenemedi: {$name}", ['error' => $e->getMessage()]);
            }
        }
        
        return null;
    }
    
    /**
     * Sistemdeki tüm ServiceProvider'ları yükle
     */
    public function registerServiceProviders(): void
    {
        $pluginsDir = config('core.plugins_directory', app_path('Plugins'));
        
        // Plugins dizini var mı kontrol et
        if (!File::isDirectory($pluginsDir)) {
            return;
        }
        
        // Tüm plugin dizinlerini oku
        $dirs = File::directories($pluginsDir);
        
        foreach ($dirs as $dir) {
            $name = basename($dir);
            
            // ServiceProvider sınıfını oluştur
            $className = "\\App\\Plugins\\{$name}\\{$name}ServiceProvider";
            
            if (class_exists($className)) {
                app()->register($className);
                Log::info("ServiceProvider kaydedildi: {$className}");
            }
        }
    }
}