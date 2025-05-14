<?php

// Scope düzeltme script'i - Kalan tüm scope metodları için
// Bu script tüm modellerdeki kalan scope metodlarını düzeltir (scopeActive gibi özel durumlar dışında)

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

// Dosyayı scope metodları için kontrol et ve düzelt
function processFile($filePath, &$affectedFiles) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Model sınıfını içeren dosyaları bul
    if (strpos($content, 'class ') !== false && 
        strpos($content, 'extends BaseModel') !== false) {
        
        // Builder kullanımını kontrol et
        $hasBuilderImport = preg_match('/use\s+Illuminate\\\\Database\\\\Eloquent\\\\Builder/', $content);
        
        // Builder importunu ekle, eğer yoksa
        if (!$hasBuilderImport && preg_match('/public\s+function\s+scope[A-Z][a-zA-Z0-9_]+\s*\(\s*\$query\s*(?:,|\))/', $content)) {
            $content = preg_replace(
                '/(namespace\s+[^;]+;\s+)/s',
                "$1\nuse Illuminate\\Database\\Eloquent\\Builder;\n",
                $content
            );
        }
        
        // Tüm scope metodlarını bul ve güncelle
        preg_match_all('/public\s+function\s+(scope[A-Z][a-zA-Z0-9_]+)\s*\(\s*\$query(?:\s*,\s*(.*?))?\s*\)\s*{/s', $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $methodName = $match[0]; // Tam method imzası
            $scopeName = $match[1]; // Scope metot adı
            
            // scopeActive hariç tüm scope metodlarını güncelle (bu metodla zaten ilgilenildi)
            if ($scopeName !== 'scopeActive') {
                if (isset($match[2]) && !empty($match[2])) {
                    // Parametreli scope metodu
                    $parameters = $match[2];
                    $updatedMethod = "public function {$scopeName}(Builder \$query, {$parameters}): Builder {";
                } else {
                    // Parametresiz scope metodu
                    $updatedMethod = "public function {$scopeName}(Builder \$query): Builder {";
                }
                
                $content = str_replace($methodName, $updatedMethod, $content);
            }
        }
        
        // Değişiklik yapıldıysa dosyayı güncelle
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $affectedFiles[] = $filePath;
            
            echo "Düzeltildi: " . $filePath . PHP_EOL;
        }
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