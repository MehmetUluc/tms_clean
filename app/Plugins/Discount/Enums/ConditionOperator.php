<?php

namespace App\Plugins\Discount\Enums;

enum ConditionOperator: string
{
    case EQUALS = 'equals';               // Eşittir
    case GREATER_THAN = 'greater_than';   // Büyüktür
    case LESS_THAN = 'less_than';         // Küçüktür
    case BETWEEN = 'between';             // Arasında
    case IN = 'in';                       // İçinde
    case NOT_IN = 'not_in';               // İçinde değil
    
    public function getLabel(): string
    {
        return match($this) {
            self::EQUALS => 'Eşittir',
            self::GREATER_THAN => 'Büyüktür',
            self::LESS_THAN => 'Küçüktür',
            self::BETWEEN => 'Arasında',
            self::IN => 'İçinde',
            self::NOT_IN => 'İçinde Değil',
        };
    }
    
    public function requiresArrayValue(): bool
    {
        return match($this) {
            self::BETWEEN, self::IN, self::NOT_IN => true,
            default => false,
        };
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $operator) => [$operator->value => $operator->getLabel()])
            ->toArray();
    }
}