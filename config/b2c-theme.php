<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration options for the B2C theme.
    |
    */

    // Temel ayarlar
    'active' => true,  // Bu değer false olduğunda, ana uygulama rotaları kullanılacaktır
    'enabled' => true, // Theme features enablement
    'route_prefix' => '',  // Örn: 'booking' yazarsanız tüm rotalar booking/ ile başlar
    'override_default_routes' => true, // Ana uygulamanın rotalarını geçersiz kılsın mı?
    
    // Tema renk şeması
    'colors' => [
        'primary' => '#3b82f6',    // Mavi
        'secondary' => '#10b981',  // Yeşil
        'accent' => '#8b5cf6',     // Mor
        'warning' => '#f59e0b',    // Turuncu
        'danger' => '#ef4444',     // Kırmızı
        'success' => '#10b981',    // Yeşil
        'info' => '#3b82f6',       // Mavi
    ],
    
    // Logo ve favicon ayarları
    'logo' => [
        'light' => '/vendor/b2c-theme/images/logo-light.png',
        'dark' => '/vendor/b2c-theme/images/logo-dark.png',
        'favicon' => '/vendor/b2c-theme/images/favicon.ico',
    ],
    
    // Sosyal medya linkleri
    'social_media' => [
        'facebook' => 'https://facebook.com/yourcompany',
        'twitter' => 'https://twitter.com/yourcompany',
        'instagram' => 'https://instagram.com/yourcompany',
        'linkedin' => 'https://linkedin.com/company/yourcompany',
    ],
    
    // İletişim bilgileri
    'contact' => [
        'email' => 'info@example.com',
        'phone' => '+90 212 123 4567',
        'address' => 'Your Company Address',
    ],
    
    // Ana sayfa ayarları
    'home' => [
        'hero_title' => 'Find Your Perfect Hotel',
        'hero_subtitle' => 'Search and book hotels at the best prices',
        'featured_hotels_count' => 8,  // Ana sayfada gösterilecek öne çıkan otel sayısı
        'featured_regions_count' => 6, // Ana sayfada gösterilecek bölge sayısı
    ],
    
    // SEO ayarları
    'seo' => [
        'site_name' => 'Your Hotel Booking Site',
        'meta_description' => 'Find and book hotels at the best prices for your next trip',
        'meta_keywords' => 'hotels, booking, travel, accommodation',
        'og_image' => '/vendor/b2c-theme/images/og-image.jpg',
        'twitter_card' => 'summary_large_image',
    ],
    
    // Tema varyantları
    'variants' => [
        'default' => 'modern',  // modern, classic, luxury
    ],
    
    // Dil ayarları
    'languages' => [
        'default' => 'en',
        'available' => ['en', 'tr', 'de', 'ru'],
    ],
    
    // Footer ayarları
    'footer' => [
        'copyright' => '© ' . date('Y') . ' Your Company. All rights reserved.',
        'show_payment_icons' => true,
        'payment_methods' => ['visa', 'mastercard', 'amex', 'paypal'],
    ],
    
    // Arama kutusu ayarları
    'search_box' => [
        'default_adults' => 2,
        'default_children' => 0,
        'max_rooms' => 5,
        'max_adults' => 10,
        'max_children' => 6,
        'advanced_filters' => true,
    ]
];