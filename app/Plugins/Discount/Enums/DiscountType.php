<?php

namespace App\Plugins\Discount\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';           // Yüzdelik indirim
    case FIXED_AMOUNT = 'fixed_amount';       // Sabit tutar indirimi
    case FREE_NIGHTS = 'free_nights';         // Ücretsiz gece (X gece kal Y gece öde)
    case NTH_NIGHT_FREE = 'nth_night_free';   // N'inci gece ücretsiz
    case PACKAGE_DEAL = 'package_deal';       // Paket indirimi
    case EARLY_BOOKING = 'early_booking';     // Erken rezervasyon
    case LAST_MINUTE = 'last_minute';         // Son dakika
    case LOYALTY = 'loyalty';                 // Sadakat indirimi
    case GROUP = 'group';                     // Grup indirimi
    case SEASONAL = 'seasonal';               // Sezonsal indirim
    case MEMBERSHIP = 'membership';           // Üyelik indirimi
    
    public function getLabel(): string
    {
        return match($this) {
            self::PERCENTAGE => 'Yüzde İndirim',
            self::FIXED_AMOUNT => 'Sabit Tutar İndirimi',
            self::FREE_NIGHTS => 'Ücretsiz Gece',
            self::NTH_NIGHT_FREE => 'N\'inci Gece Ücretsiz',
            self::PACKAGE_DEAL => 'Paket İndirimi',
            self::EARLY_BOOKING => 'Erken Rezervasyon',
            self::LAST_MINUTE => 'Son Dakika',
            self::LOYALTY => 'Sadakat İndirimi',
            self::GROUP => 'Grup İndirimi',
            self::SEASONAL => 'Sezonsal İndirim',
            self::MEMBERSHIP => 'Üyelik İndirimi',
        };
    }
    
    public function requiresAdditionalConfig(): bool
    {
        return match($this) {
            self::FREE_NIGHTS, self::NTH_NIGHT_FREE, 
            self::PACKAGE_DEAL, self::EARLY_BOOKING,
            self::LAST_MINUTE => true,
            default => false,
        };
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->getLabel()])
            ->toArray();
    }
}