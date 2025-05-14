<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ThemeManager Configuration
    |--------------------------------------------------------------------------
    |
    | Tema yöneticisi için temel yapılandırma ayarları.
    |
    */
    
    // Temel ayarlar
    'cache_duration' => 60 * 5, // 5 dakika (saniye cinsinden)
    
    // Varsayılan tema ayarları. Veritabanı boş olduğunda bu değerler kullanılır.
    'defaults' => [
        // Site Bilgileri
        'site_name' => [
            'value' => 'Hotel Booking Site',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ],
        'site_description' => [
            'value' => 'Find and book hotels at the best prices',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ],
        
        // Tema Renkleri
        'color_primary' => [
            'value' => '#3b82f6',
            'type' => 'color',
            'group' => 'colors',
            'is_public' => true,
        ],
        'color_secondary' => [
            'value' => '#10b981',
            'type' => 'color',
            'group' => 'colors',
            'is_public' => true,
        ],
        'color_accent' => [
            'value' => '#8b5cf6',
            'type' => 'color',
            'group' => 'colors',
            'is_public' => true,
        ],
        'color_warning' => [
            'value' => '#f59e0b',
            'type' => 'color',
            'group' => 'colors',
            'is_public' => true,
        ],
        'color_danger' => [
            'value' => '#ef4444',
            'type' => 'color',
            'group' => 'colors',
            'is_public' => true,
        ],
        'color_success' => [
            'value' => '#10b981',
            'type' => 'color',
            'group' => 'colors',
            'is_public' => true,
        ],
        'color_info' => [
            'value' => '#3b82f6',
            'type' => 'color',
            'group' => 'colors',
            'is_public' => true,
        ],
        
        // Logo ve Görseller
        'logo_light' => [
            'value' => '/vendor/b2c-theme/images/logo-light.png',
            'type' => 'image',
            'group' => 'logos',
            'is_public' => true,
        ],
        'logo_dark' => [
            'value' => '/vendor/b2c-theme/images/logo-dark.png',
            'type' => 'image',
            'group' => 'logos',
            'is_public' => true,
        ],
        'favicon' => [
            'value' => '/vendor/b2c-theme/images/favicon.ico',
            'type' => 'image',
            'group' => 'logos',
            'is_public' => true,
        ],
        
        // SEO Ayarları
        'seo_meta_description' => [
            'value' => 'Find and book hotels at the best prices for your next trip',
            'type' => 'string',
            'group' => 'seo',
            'is_public' => true,
        ],
        'seo_meta_keywords' => [
            'value' => 'hotels, booking, travel, accommodation',
            'type' => 'string',
            'group' => 'seo',
            'is_public' => true,
        ],
        'seo_og_image' => [
            'value' => '/vendor/b2c-theme/images/og-image.jpg',
            'type' => 'image',
            'group' => 'seo',
            'is_public' => true,
        ],
        'seo_twitter_card' => [
            'value' => 'summary_large_image',
            'type' => 'string',
            'group' => 'seo',
            'is_public' => true,
        ],
        
        // Sosyal Medya Linkleri
        'social_facebook' => [
            'value' => 'https://facebook.com/yourcompany',
            'type' => 'string',
            'group' => 'social',
            'is_public' => true,
        ],
        'social_twitter' => [
            'value' => 'https://twitter.com/yourcompany',
            'type' => 'string',
            'group' => 'social',
            'is_public' => true,
        ],
        'social_instagram' => [
            'value' => 'https://instagram.com/yourcompany',
            'type' => 'string',
            'group' => 'social',
            'is_public' => true,
        ],
        'social_linkedin' => [
            'value' => 'https://linkedin.com/company/yourcompany',
            'type' => 'string',
            'group' => 'social',
            'is_public' => true,
        ],
        
        // İletişim Bilgileri
        'contact_email' => [
            'value' => 'info@example.com',
            'type' => 'string',
            'group' => 'contact',
            'is_public' => true,
        ],
        'contact_phone' => [
            'value' => '+90 212 123 4567',
            'type' => 'string',
            'group' => 'contact',
            'is_public' => true,
        ],
        'contact_address' => [
            'value' => 'Örnek Şirket Adresi',
            'type' => 'string',
            'group' => 'contact',
            'is_public' => true,
        ],
        
        // Footer Ayarları
        'footer_copyright' => [
            'value' => '© ' . date('Y') . ' Your Company. All rights reserved.',
            'type' => 'string',
            'group' => 'layout',
            'is_public' => true,
        ],
        'footer_payment_methods' => [
            'value' => json_encode(['visa', 'mastercard', 'amex', 'paypal']),
            'type' => 'json',
            'group' => 'layout',
            'is_public' => true,
        ],
        
        // Typografi Ayarları
        'typography_heading_font' => [
            'value' => 'Inter, sans-serif',
            'type' => 'string',
            'group' => 'typography',
            'is_public' => true,
        ],
        'typography_body_font' => [
            'value' => 'Inter, sans-serif',
            'type' => 'string',
            'group' => 'typography',
            'is_public' => true,
        ],
        
        // Ana Sayfa Ayarları
        'home_hero_title' => [
            'value' => 'Sizin İçin En Uygun Oteli Bulun',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ],
        'home_hero_subtitle' => [
            'value' => 'En uygun fiyatlarla otel arayın ve rezervasyon yapın',
            'type' => 'string',
            'group' => 'general',
            'is_public' => true,
        ],
    ],
    
    // Gruplar ve başlıkları
    'groups' => [
        'general' => 'Genel Ayarlar',
        'colors' => 'Renk Şeması',
        'logos' => 'Logo ve Görseller',
        'seo' => 'SEO Ayarları',
        'social' => 'Sosyal Medya',
        'contact' => 'İletişim Bilgileri',
        'layout' => 'Sayfa Düzeni',
        'typography' => 'Yazı Tipi Ayarları',
    ],
    
    // İzin verilen renk paleti (tema yöneticisi formunda gösterilecek renk seçenekleri)
    'color_palette' => [
        '#3b82f6' => 'Mavi',
        '#10b981' => 'Yeşil',
        '#8b5cf6' => 'Mor',
        '#f59e0b' => 'Turuncu',
        '#ef4444' => 'Kırmızı',
        '#64748b' => 'Gri',
        '#0f172a' => 'Lacivert',
        '#000000' => 'Siyah',
    ],
];