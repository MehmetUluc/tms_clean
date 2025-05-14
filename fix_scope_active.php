<?php

// Scope düzeltme script'i
// Bu script tüm modellerdeki scopeActive metodunu BaseModel ile uyumlu hale getirir

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Düzeltilecek dizin
$baseDir = __DIR__ . '/packages/Tms/';

// Düzeltmeden etkilenen dosyalar
$affectedFiles = [];

// Özyinelemeli olarak php dosyalarını tara
function scanPhpFiles($dir, &$affectedFiles) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            scanPhpFiles($path, $affectedFiles);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            processFile($path, $affectedFiles);
        }
    }
}

// Dosyayı scopeActive metodu için kontrol et ve düzelt
function processFile($filePath, &$affectedFiles) {
    $content = file_get_contents($filePath);
    
    // Model sınıfını içeren ve scopeActive metodu olan dosyaları bul
    if (strpos($content, 'class ') !== false && 
        strpos($content, 'extends BaseModel') !== false && 
        preg_match('/public\s+function\s+scopeActive\s*\(\s*\$query\s*\)/', $content)) {
        
        // Builder kullanımını kontrol et
        $hasBuilderImport = preg_match('/use\s+Illuminate\\\\Database\\\\Eloquent\\\\Builder/', $content);
        
        // Builder importunu ekle, eğer yoksa
        if (!$hasBuilderImport) {
            $content = preg_replace(
                '/(namespace\s+[^;]+;\s+)/s',
                "$1\nuse Illuminate\\Database\\Eloquent\\Builder;\n",
                $content
            );
        }
        
        // scopeActive metodunu güncelle
        $content = preg_replace(
            '/public\s+function\s+scopeActive\s*\(\s*\$query\s*\)\s*{/',
            'public function scopeActive(Builder $query): Builder {',
            $content
        );
        
        // Dosyayı güncelle
        file_put_contents($filePath, $content);
        $affectedFiles[] = $filePath;
        
        echo "Düzeltildi: " . $filePath . PHP_EOL;
    }
}

// Başla
echo "Düzeltme başlatılıyor...\n";
scanPhpFiles($baseDir, $affectedFiles);

if (count($affectedFiles) > 0) {
    echo "\nToplam " . count($affectedFiles) . " dosya düzeltildi:\n";
    foreach ($affectedFiles as $file) {
        echo "- " . str_replace(__DIR__ . '/', '', $file) . "\n";
    }
} else {
    echo "\nDüzeltilecek dosya bulunamadı.\n";
}

echo "\nDüzeltme tamamlandı.\n";