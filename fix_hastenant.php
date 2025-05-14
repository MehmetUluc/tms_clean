<?php

/**
 * Bu script, Tms\Core\Models\BaseModel'den türeyen tüm sınıflarda
 * $hasTenant özelliğini static yapar.
 */

$packageDir = __DIR__ . '/packages';
$fixedFiles = [];

function findPhpFiles($dir) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    $files = [];
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

function fixHasTenantInFile($file) {
    $content = file_get_contents($file);
    
    // protected $hasTenant değişkenini protected static $hasTenant ile değiştir
    $pattern = '/protected\s+\$hasTenant\s*=/';
    $replacement = 'protected static $hasTenant =';
    
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($content !== $newContent) {
        file_put_contents($file, $newContent);
        return true;
    }
    
    return false;
}

$phpFiles = findPhpFiles($packageDir);

foreach ($phpFiles as $file) {
    if (fixHasTenantInFile($file)) {
        $fixedFiles[] = $file;
    }
}

echo "Fixed " . count($fixedFiles) . " files.\n";
foreach ($fixedFiles as $file) {
    echo "- " . $file . "\n";
}