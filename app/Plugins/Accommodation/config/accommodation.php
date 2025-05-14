<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Accommodation Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, Accommodation plugin'in davranışını ve özelliklerini kontrol eder.
    |
    */

    // Medya yükleme ayarları
    'media' => [
        'hotels' => [
            'cover_path' => 'hotels/covers',
            'gallery_path' => 'hotels/gallery',
            'max_images' => 20,
            'disk' => 'public',
        ],
        'rooms' => [
            'cover_path' => 'rooms/covers',
            'gallery_path' => 'rooms/gallery',
            'max_images' => 10,
            'disk' => 'public',
        ],
    ],
    
    // Arama filtreleri
    'search' => [
        'hotel_fields' => ['name', 'description', 'address', 'city', 'country'],
        'room_fields' => ['name', 'description'],
        'enable_fuzzy_search' => true,
    ],
    
    // Navigasyon grubu ayarları
    'navigation' => [
        'group' => 'Accommodation', // Menü grup adı
        'icon' => 'heroicon-o-building-office-2', // Varsayılan ikon
        'sort' => 2, // Menü sıralaması
    ],
    
    // Otel ayarları
    'hotels' => [
        'enable_ratings' => true,
        'enable_reviews' => true,
        'default_check_in_time' => '14:00',
        'default_check_out_time' => '12:00',
    ],
    
    // Oda ayarları
    'rooms' => [
        'enable_inventory' => true,
        'default_capacity' => 2,
    ],
];