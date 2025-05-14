<?php

namespace App\Plugins\Discount\Enums;

enum ConditionType: string
{
    case MIN_STAY = 'min_stay';                // Minimum kalış
    case SPECIFIC_DAYS = 'specific_days';     // Belirli günler
    case ADVANCE_BOOKING = 'advance_booking';  // Erken rezervasyon
    case FIRST_BOOKING = 'first_booking';      // İlk rezervasyon
    case REPEAT_CUSTOMER = 'repeat_customer';  // Tekrar müşteri
    case MEMBERSHIP = 'membership';            // Üyelik
    case SPECIFIC_ROOM = 'specific_room';      // Belirli oda/oda tipi
    case WEEKDAY = 'weekday';                  // Haftaiçi/Haftasonu
    case OCCUPANCY = 'occupancy';              // Doluluk durumu
    case SEASON = 'season';                    // Sezon
    case SPECIAL_EVENT = 'special_event';      // Özel etkinlik
    
    public function getLabel(): string
    {
        return match($this) {
            self::MIN_STAY => 'Minimum Kalış',
            self::SPECIFIC_DAYS => 'Belirli Günler',
            self::ADVANCE_BOOKING => 'Erken Rezervasyon',
            self::FIRST_BOOKING => 'İlk Rezervasyon',
            self::REPEAT_CUSTOMER => 'Tekrar Müşteri',
            self::MEMBERSHIP => 'Üyelik',
            self::SPECIFIC_ROOM => 'Belirli Oda/Oda Tipi',
            self::WEEKDAY => 'Haftaiçi/Haftasonu',
            self::OCCUPANCY => 'Doluluk Durumu',
            self::SEASON => 'Sezon',
            self::SPECIAL_EVENT => 'Özel Etkinlik',
        };
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->getLabel()])
            ->toArray();
    }
}