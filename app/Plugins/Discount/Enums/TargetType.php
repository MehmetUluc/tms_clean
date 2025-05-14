<?php

namespace App\Plugins\Discount\Enums;

enum TargetType: string
{
    case ALL = 'all';                     // Tüm oteller
    case HOTEL = 'hotel';                 // Belirli otel
    case ROOM_TYPE = 'room_type';         // Oda tipi
    case BOARD_TYPE = 'board_type';       // Pansiyon tipi
    case PACKAGE = 'package';             // Paket
    case SERVICE = 'service';             // Ek hizmet

    public function getLabel(): string
    {
        return match($this) {
            self::ALL => 'Tüm Oteller',
            self::HOTEL => 'Belirli Otel',
            self::ROOM_TYPE => 'Oda Tipi',
            self::BOARD_TYPE => 'Pansiyon Tipi',
            self::PACKAGE => 'Paket',
            self::SERVICE => 'Ek Hizmet',
        };
    }
    
    public function requiresTargetId(): bool
    {
        return $this !== self::ALL;
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->getLabel()])
            ->toArray();
    }
}