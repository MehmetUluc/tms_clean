<?php

namespace App\Plugins\Discount\Services\Calculators;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Models\Discount;

class LongStayDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for a long stay discount
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculate(Discount $discount, float $price, array $context = []): float
    {
        // The value field contains the base percentage discount
        $percentage = $discount->value;
        
        // Get the configuration values
        $config = $discount->configuration ?? [];
        $minNights = $config['min_nights'] ?? 7; // Default: 7 nights
        $additionalPercentPerExtraNight = $config['additional_percent_per_extra_night'] ?? 0;
        $maxAdditionalPercent = $config['max_additional_percent'] ?? 0;
        
        // Get the total nights from context
        $totalNights = $context['nights'] ?? 0;
        
        // If not enough nights for long stay, no discount
        if ($totalNights < $minNights) {
            return 0;
        }
        
        // Calculate additional percentage based on extra nights
        $extraNights = $totalNights - $minNights;
        $additionalPercent = $extraNights * $additionalPercentPerExtraNight;
        
        // Apply max additional percentage if set
        if ($maxAdditionalPercent > 0 && $additionalPercent > $maxAdditionalPercent) {
            $additionalPercent = $maxAdditionalPercent;
        }
        
        // Calculate total percentage
        $totalPercentage = $percentage + $additionalPercent;
        
        // Calculate discount amount
        $discountAmount = ($totalPercentage / 100) * $price;
        
        // If there's a max value, apply it
        if ($discount->max_value && $discountAmount > $discount->max_value) {
            $discountAmount = $discount->max_value;
        }
        
        // Ensure the discount doesn't exceed the original price
        return min($discountAmount, $price);
    }
}