<?php

namespace App\Plugins\Discount\Services\Calculators;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Models\Discount;

class FreeNightsDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for a free nights discount
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculate(Discount $discount, float $price, array $context = []): float
    {
        // The value field contains the number of free nights
        $freeNights = (int) $discount->value;
        
        // Get the total nights from context
        $totalNights = $context['nights'] ?? 0;
        
        // If no night information or not enough nights, no discount
        if ($totalNights <= 0 || $totalNights <= $freeNights) {
            return 0;
        }
        
        // Get price per night (assuming equal distribution)
        $pricePerNight = $price / $totalNights;
        
        // Calculate discount amount
        $discountAmount = $freeNights * $pricePerNight;
        
        // If there's a max value, apply it
        if ($discount->max_value && $discountAmount > $discount->max_value) {
            $discountAmount = $discount->max_value;
        }
        
        // Ensure the discount doesn't exceed the original price
        return min($discountAmount, $price);
    }
}