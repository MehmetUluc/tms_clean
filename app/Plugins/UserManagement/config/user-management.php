<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Management Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | Bu ayarlar, User Management plugin'in davranışını ve özelliklerini kontrol eder.
    |
    */

    // Kullanıcı ayarları
    'users' => [
        'default_role' => 'user',
        'admin_role' => 'admin',
        'super_admin_role' => 'super-admin',
        'require_email_verification' => true,
        'password_requirements' => [
            'min_length' => 8,
            'require_numbers' => true,
            'require_symbols' => true,
            'require_uppercase' => true,
        ],
        'session_lifetime' => 120, // dakika
    ],
    
    // Rol ayarları
    'roles' => [
        'super_admin_editable' => false,
        'default_permissions' => [
            'user' => [
                'user.view',
                'user.view.own',
                'user.update.own',
            ],
            'admin' => [
                'user.view',
                'user.create',
                'user.update',
                'user.delete',
                'role.view',
            ],
        ],
    ],
    
    // İki faktörlü kimlik doğrulama
    '2fa' => [
        'enabled' => false,
        'enforce_for_admins' => false, 
    ],
    
    // Navigasyon grubu ayarları
    'navigation' => [
        'group' => 'Kullanıcı Yönetimi', // Menü grup adı
        'icon' => 'heroicon-o-user-group', // Varsayılan ikon
        'sort' => 99, // Menü sıralaması (en sonda)
    ],
];