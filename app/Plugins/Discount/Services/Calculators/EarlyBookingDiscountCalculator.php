<?php

namespace App\Plugins\Discount\Services\Calculators;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Models\Discount;
use Carbon\Carbon;

class EarlyBookingDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for an early booking discount
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
        $daysThreshold = $config['days_threshold'] ?? null;
        $additionalPercentPerDay = $config['additional_percent_per_day'] ?? 0;
        $maxAdditionalPercent = $config['max_additional_percent'] ?? 0;
        
        // Get check-in date from context
        $checkInDate = $context['check_in_date'] ?? null;
        
        // If no check-in date or days threshold, apply base percentage only
        if (!$checkInDate || !$daysThreshold) {
            return ($percentage / 100) * $price;
        }
        
        // Parse dates
        $checkInCarbon = $checkInDate instanceof Carbon 
            ? $checkInDate 
            : Carbon::parse($checkInDate);
        $today = Carbon::today();
        
        // Calculate days until check-in
        $daysUntilCheckIn = $today->diffInDays($checkInCarbon);
        
        // If booking is not early enough, apply base percentage only
        if ($daysUntilCheckIn <= $daysThreshold) {
            return ($percentage / 100) * $price;
        }
        
        // Calculate additional percentage based on extra days
        $extraDays = $daysUntilCheckIn - $daysThreshold;
        $additionalPercent = $extraDays * $additionalPercentPerDay;
        
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