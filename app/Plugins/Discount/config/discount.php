<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Discount System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the discount system.
    |
    */

    // Enable or disable the discount system
    'enabled' => true,
    
    // Default currency for fixed amount discounts
    'default_currency' => 'TRY',
    
    // Maximum discount percentage allowed
    'max_percentage' => 100,
    
    // Maximum fixed amount discount (0 = no limit)
    'max_fixed_amount' => 0,
    
    // Stack behavior for multiple discounts
    // Options: 'best_discount', 'all_applicable', 'custom'
    'stack_behavior' => 'all_applicable',
    
    // Form field configurations for different discount types
    'discount_type_fields' => [
        'percentage' => [
            'value_label' => 'Percentage (%)',
            'value_min' => 0,
            'value_max' => 100,
            'value_default' => 10,
            'max_value_enabled' => true,
        ],
        'fixed_amount' => [
            'value_label' => 'Amount',
            'value_min' => 0,
            'value_max' => null,
            'value_default' => 100,
            'max_value_enabled' => false,
        ],
        'free_nights' => [
            'value_label' => 'Number of Free Nights',
            'value_min' => 1,
            'value_max' => null,
            'value_default' => 1,
            'max_value_enabled' => true,
        ],
        'nth_night_free' => [
            'value_label' => 'Every Nth Night Free',
            'value_min' => 2,
            'value_max' => null,
            'value_default' => 3,
            'max_value_enabled' => true,
        ],
        'early_booking' => [
            'value_label' => 'Percentage (%)',
            'value_min' => 0,
            'value_max' => 100,
            'value_default' => 15,
            'max_value_enabled' => true,
        ],
        'last_minute' => [
            'value_label' => 'Percentage (%)',
            'value_min' => 0,
            'value_max' => 100,
            'value_default' => 20,
            'max_value_enabled' => true,
        ],
        'long_stay' => [
            'value_label' => 'Percentage (%)',
            'value_min' => 0,
            'value_max' => 100,
            'value_default' => 15,
            'max_value_enabled' => true,
        ],
        'package_deal' => [
            'value_label' => 'Percentage (%)',
            'value_min' => 0,
            'value_max' => 100,
            'value_default' => 15,
            'max_value_enabled' => true,
        ],
    ],
    
    // Preset discount definitions
    'presets' => [
        'early_booking' => [
            'name' => 'Erken Rezervasyon İndirimi',
            'description' => 'Erken rezervasyonlarda özel indirim. Ne kadar erken rezervasyon yaparsanız, o kadar fazla tasarruf edersiniz.',
            'discount_type' => 'early_booking',
            'value' => 10,
            'stack_type' => 'exclusive',
            'configuration' => [
                'days_threshold' => 30,
                'additional_percent_per_day' => 0.2,
                'max_additional_percent' => 10,
            ],
        ],
        'last_minute' => [
            'name' => 'Son Dakika İndirimi',
            'description' => 'Son dakika rezervasyonlarında özel indirim.',
            'discount_type' => 'last_minute',
            'value' => 15,
            'stack_type' => 'exclusive',
            'configuration' => [
                'max_days_before_check_in' => 7,
                'min_days_before_check_in' => 0,
                'additional_percent_per_day_closer' => 1,
            ],
        ],
        'stay_3_pay_2' => [
            'name' => '3 Gece Kal 2 Gece Öde',
            'description' => '3 gece konakla, sadece 2 gece için ödeme yap.',
            'discount_type' => 'free_nights',
            'value' => 1,
            'stack_type' => 'exclusive',
        ],
        'third_night_free' => [
            'name' => 'Her 3. Gece Bedava',
            'description' => 'Konaklamanızın her 3. gecesi ücretsiz.',
            'discount_type' => 'nth_night_free',
            'value' => 3,
            'stack_type' => 'exclusive',
            'configuration' => [
                'max_free_nights' => 3,
            ],
        ],
        'long_stay_discount' => [
            'name' => 'Uzun Konaklama İndirimi',
            'description' => '7 gece ve üzeri konaklamalarda özel indirim.',
            'discount_type' => 'long_stay',
            'value' => 10,
            'stack_type' => 'stackable',
            'configuration' => [
                'min_nights' => 7,
                'additional_percent_per_extra_night' => 0.5,
                'max_additional_percent' => 15,
            ],
        ],
        'weekend_special' => [
            'name' => 'Haftasonu Özel',
            'description' => 'Haftasonu konaklamalarında %10 indirim (Cuma veya Cumartesi giriş).',
            'discount_type' => 'percentage',
            'value' => 10,
            'stack_type' => 'stackable',
        ],
        'holiday_package' => [
            'name' => 'Tatil Paketi',
            'description' => 'Oda, havaalanı transferi ve kahvaltı dahil özel paket fiyatı.',
            'discount_type' => 'package_deal',
            'value' => 15,
            'stack_type' => 'exclusive',
            'configuration' => [
                'required_services' => ['breakfast', 'airport_transfer'],
            ],
        ],
        'summer_sale' => [
            'name' => 'Yaz Kampanyası',
            'description' => 'Tüm yaz rezervasyonlarında %20 indirim.',
            'discount_type' => 'percentage',
            'value' => 20,
            'stack_type' => 'exclusive',
        ],
        'fixed_discount' => [
            'name' => 'Sabit Tutar İndirimi',
            'description' => 'Rezervasyonunuzda sabit tutar indirimi.',
            'discount_type' => 'fixed_amount',
            'value' => 100,
            'stack_type' => 'stackable',
        ],
        'loyal_guest' => [
            'name' => 'Sadık Misafir İndirimi',
            'description' => 'Tekrar gelen misafirlerimize özel indirim.',
            'discount_type' => 'percentage',
            'value' => 10,
            'stack_type' => 'stackable',
        ],
        'family_discount' => [
            'name' => 'Aile İndirimi',
            'description' => '3+ kişilik konaklamalarda indirim.',
            'discount_type' => 'percentage',
            'value' => 8,
            'stack_type' => 'stackable',
        ],
    ],
    
    // Indirim formları için görünüm ayarları
    'forms' => [
        'discount_types_with_additional_fields' => [
            'free_nights' => [
                'fields' => [
                    'stay_nights' => [
                        'type' => 'number',
                        'label' => 'Kalınacak Gece',
                        'min' => 2,
                        'required' => true,
                    ],
                    'pay_nights' => [
                        'type' => 'number',
                        'label' => 'Ödenecek Gece',
                        'min' => 1,
                        'required' => true,
                    ],
                ],
            ],
            'nth_night_free' => [
                'fields' => [
                    'every_nth_night' => [
                        'type' => 'number',
                        'label' => 'Her Kaçıncı Gece',
                        'min' => 2,
                        'required' => true,
                    ],
                ],
            ],
            'early_booking' => [
                'fields' => [
                    'days_in_advance' => [
                        'type' => 'number',
                        'label' => 'Kaç Gün Öncesinde',
                        'min' => 1,
                        'required' => true,
                    ],
                ],
            ],
            'last_minute' => [
                'fields' => [
                    'days_before' => [
                        'type' => 'number',
                        'label' => 'Kaç Gün Öncesine Kadar',
                        'min' => 1,
                        'required' => true,
                    ],
                ],
            ],
        ],
    ],
];