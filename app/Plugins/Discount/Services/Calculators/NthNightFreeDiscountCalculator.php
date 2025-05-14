<?php

namespace App\Plugins\Discount\Services\Calculators;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Models\Discount;

class NthNightFreeDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for an nth night free discount
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculate(Discount $discount, float $price, array $context = []): float
    {
        // The value field contains which night is free (e.g., 3 for "3rd night free")
        $nthNight = (int) $discount->value;
        
        // Get the configuration values
        $config = $discount->configuration ?? [];
        $maxFreeNights = $config['max_free_nights'] ?? 0;
        
        // Get the total nights from context
        $totalNights = $context['nights'] ?? 0;
        
        // If no night information, not enough nights, or invalid nth night, no discount
        if ($totalNights <= 0 || $totalNights < $nthNight || $nthNight <= 0) {
            return 0;
        }
        
        // Calculate how many free nights are applicable
        $freeNightsCount = floor($totalNights / $nthNight);
        
        // Apply maximum free nights limit if set
        if ($maxFreeNights > 0 && $freeNightsCount > $maxFreeNights) {
            $freeNightsCount = $maxFreeNights;
        }
        
        // Get price per night (assuming equal distribution)
        $pricePerNight = $price / $totalNights;
        
        // Calculate discount amount
        $discountAmount = $freeNightsCount * $pricePerNight;
        
        // If there's a max value, apply it
        if ($discount->max_value && $discountAmount > $discount->max_value) {
            $discountAmount = $discount->max_value;
        }
        
        // Ensure the discount doesn't exceed the original price
        return min($discountAmount, $price);
    }
}