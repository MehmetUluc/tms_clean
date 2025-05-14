<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Core Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, Core plugin'in davranışını ve özelliklerini kontrol eder.
    |
    */

    // Plugins dizini
    'plugins_directory' => app_path('Plugins'),
    
    // Otomatik olarak yüklenecek plugin'ler
    'auto_load_plugins' => true,
    
    // Plugin'ler için tenant izolasyonu
    'tenant_isolation' => true,
    
    // Medya yükleme ayarları
    'media' => [
        'disk' => 'public',
        'directory' => 'uploads',
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'max_size' => 10240, // 10MB
    ],
    
    // Cache ayarları
    'cache' => [
        'enabled' => true,
        'prefix' => 'core_',
        'ttl' => 60 * 60 * 24, // 1 gün
    ],
    
    // Log ayarları
    'logging' => [
        'channel' => 'stack',
        'level' => 'debug',
    ],
    
    // Form bileşenleri ayarları
    'form_components' => [
        'file_upload' => [
            'show_as_grid' => true,
            'grid_columns' => 4,
            'thumbnail_size' => 150,
        ],
    ],
];