<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Integration Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, Integration plugin'in davranışını ve özelliklerini kontrol eder.
    |
    */

    // API ayarları
    'api' => [
        'versions' => ['v1'],
        'default_version' => 'v1',
        'rate_limits' => [
            'default' => [
                'limit' => 60,
                'period' => 60, // 60 istek / 60 saniye
            ],
            'high' => [
                'limit' => 120,
                'period' => 60, // 120 istek / 60 saniye
            ],
            'unlimited' => [
                'limit' => 0,
                'period' => 60,
            ],
        ],
    ],
    
    // Mapping ayarları
    'mappings' => [
        'formats' => [
            'xml' => 'XML',
            'json' => 'JSON',
            'csv' => 'CSV',
        ],
        'default_format' => 'xml',
        'mapping_types' => [
            'hotel' => 'Otel',
            'room' => 'Oda',
            'reservation' => 'Rezervasyon',
            'guest' => 'Misafir',
            'rate' => 'Fiyat',
            'availability' => 'Müsaitlik',
        ],
    ],
    
    // Credentials ayarları
    'credentials' => [
        'api_key' => [
            'length' => 32,
            'expires' => false,
        ],
        'api_secret' => [
            'length' => 64,
            'expires' => true,
            'expires_in' => 180, // 180 gün
        ],
    ],
    
    // Navigasyon grubu ayarları
    'navigation' => [
        'group' => 'Entegrasyon', // Menü grup adı
        'icon' => 'heroicon-o-bolt', // Varsayılan ikon
        'sort' => 5, // Menü sıralaması
    ],
];