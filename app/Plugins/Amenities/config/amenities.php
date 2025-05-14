<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Amenities Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, Amenities plugin'in davranışını ve özelliklerini kontrol eder.
    |
    */

    // Otel özellikleri kategorileri
    'hotel_amenity_categories' => [
        'general' => 'Genel',
        'facilities' => 'Tesisler',
        'services' => 'Hizmetler',
        'internet' => 'İnternet',
        'parking' => 'Otopark',
        'wellness' => 'Sağlık & Spor',
        'food_drink' => 'Yiyecek & İçecek',
        'business' => 'İş',
        'family' => 'Aile',
        'accessibility' => 'Erişilebilirlik',
        'sustainability' => 'Sürdürülebilirlik',
    ],
    
    // Oda özellikleri kategorileri
    'room_amenity_categories' => [
        'general' => 'Genel',
        'bathroom' => 'Banyo',
        'bedroom' => 'Yatak Odası',
        'kitchen' => 'Mutfak',
        'media' => 'Medya & Teknoloji',
        'comfort' => 'Konfor',
        'view' => 'Manzara',
        'accessibility' => 'Erişilebilirlik',
        'cleaning' => 'Temizlik',
    ],
    
    // Otel etiket kategorileri
    'hotel_tag_categories' => [
        'property_type' => 'Mülk Tipi',
        'location' => 'Konum',
        'theme' => 'Tema',
        'facility' => 'Tesis',
        'special' => 'Özel',
    ],
    
    // İkonlar
    'icons' => [
        'default_hotel_amenity' => 'heroicon-o-sparkles',
        'default_room_amenity' => 'heroicon-o-home-modern',
        'default_hotel_tag' => 'heroicon-o-tag',
    ],
    
    // Navigasyon grubu ayarları
    'navigation' => [
        'group' => 'Özellikler', // Menü grup adı
        'icon' => 'heroicon-o-sparkles', // Varsayılan ikon
        'sort' => 4, // Menü sıralaması
    ],
];