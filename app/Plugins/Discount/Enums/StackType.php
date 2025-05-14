<?php

namespace App\Plugins\Discount\Enums;

enum StackType: string
{
    case EXCLUSIVE = 'exclusive';       // Bu indirim başka indirimlerle uygulanamaz
    case STACKABLE = 'stackable';       // Bu indirim diğer indirimlerle birlikte uygulanabilir
    case FIRST_MATCH = 'first_match';   // İlk eşleşen indirim uygulanır, diğerleri uygulanmaz
    
    public function getLabel(): string
    {
        return match($this) {
            self::EXCLUSIVE => 'Özel (Diğer İndirimlerle Birleşmez)',
            self::STACKABLE => 'Birleştirilebilir (Diğer İndirimlerle Birleşir)',
            self::FIRST_MATCH => 'İlk Eşleşen (İlk Eşleşen İndirim Uygulanır)',
        };
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->getLabel()])
            ->toArray();
    }
}