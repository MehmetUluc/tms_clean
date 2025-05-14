<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OTA (Online Travel Agency) Ayarları
    |--------------------------------------------------------------------------
    |
    | Bu dosya OTA entegrasyonları için kullanılan ayarları içerir.
    |
    */

    // Varsayılan aktif durum
    'active_default' => true,
    
    // XML dönüşüm ayarları
    'xml' => [
        'pretty_print' => true,
        'encoding' => 'UTF-8',
        'version' => '1.0',
    ],
    
    // API istekleri için timeout süresi (saniye)
    'timeout' => 30,
    
    // Senkronizasyon işlemi sırasındaki maksimum deneme sayısı
    'max_retry_attempts' => 3,
    
    // Varsayılan senkronizasyon sıklığı (dakika)
    'sync_interval' => 60,
    
    // API rate limiting (dakikada maksimum istek sayısı)
    'rate_limit' => 100,
    
    // Hata bildirimleri için e-posta adresleri
    'error_notification_emails' => [
        // 'admin@example.com',
    ],
    
    // Log ayarları
    'logging' => [
        'enabled' => true,
        'level' => 'debug', // debug, info, warning, error, critical
        'channel' => 'ota',
    ],
];