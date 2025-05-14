<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Booking Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, Booking plugin'in davranışını ve özelliklerini kontrol eder.
    |
    */

    // Rezervasyon ayarları
    'reservations' => [
        'status_options' => [
            'pending' => 'Beklemede',
            'confirmed' => 'Onaylandı',
            'cancelled' => 'İptal Edildi',
            'no_show' => 'Gelmedi',
            'completed' => 'Tamamlandı',
        ],
        'payment_status_options' => [
            'unpaid' => 'Ödenmedi',
            'partially_paid' => 'Kısmen Ödendi',
            'paid' => 'Tamamen Ödendi',
            'refunded' => 'İade Edildi',
        ],
        'source_options' => [
            'direct' => 'Direkt',
            'phone' => 'Telefon',
            'email' => 'E-posta',
            'website' => 'Web Sitesi',
            'booking_com' => 'Booking.com',
            'expedia' => 'Expedia',
            'airbnb' => 'Airbnb',
            'other' => 'Diğer',
        ],
        'auto_confirm' => false,
        'confirmation_email' => true,
        'guest_email_required' => true,
    ],
    
    // Misafir ayarları
    'guests' => [
        'required_fields' => [
            'first_name' => true,
            'last_name' => true,
            'email' => true,
            'phone' => false,
            'address' => false,
            'city' => false,
            'country' => false,
            'id_number' => false,
        ],
        'id_types' => [
            'national_id' => 'Kimlik No',
            'passport' => 'Pasaport No',
            'driving_license' => 'Ehliyet No',
            'other' => 'Diğer',
        ],
    ],
    
    // Pansiyon tipleri
    'board_types' => [
        'defaults' => [
            'room_only' => 'Sadece Oda',
            'bed_breakfast' => 'Kahvaltı Dahil',
            'half_board' => 'Yarım Pansiyon',
            'full_board' => 'Tam Pansiyon',
            'all_inclusive' => 'Her Şey Dahil',
        ],
    ],
    
    // Navigasyon grubu ayarları
    'navigation' => [
        'group' => 'Rezervasyon', // Menü grup adı
        'icon' => 'heroicon-o-calendar', // Varsayılan ikon
        'sort' => 3, // Menü sıralaması
    ],
];