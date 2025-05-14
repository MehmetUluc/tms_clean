<?php

namespace App\Plugins\Discount\Services\Calculators;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Models\Discount;
use Carbon\Carbon;

class LastMinuteDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for a last minute discount
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
        $maxDaysBeforeCheckIn = $config['max_days_before_check_in'] ?? 7; // Default: 7 days
        $minDaysBeforeCheckIn = $config['min_days_before_check_in'] ?? 0; // Default: 0 days (same day)
        $additionalPercentPerDayCloser = $config['additional_percent_per_day_closer'] ?? 0;
        
        // Get check-in date from context
        $checkInDate = $context['check_in_date'] ?? null;
        
        // If no check-in date, apply base percentage only
        if (!$checkInDate) {
            return ($percentage / 100) * $price;
        }
        
        // Parse dates
        $checkInCarbon = $checkInDate instanceof Carbon 
            ? $checkInDate 
            : Carbon::parse($checkInDate);
        $today = Carbon::today();
        
        // Calculate days until check-in
        $daysUntilCheckIn = $today->diffInDays($checkInCarbon);
        
        // If not within the last minute window or too close, no discount
        if ($daysUntilCheckIn > $maxDaysBeforeCheckIn || $daysUntilCheckIn < $minDaysBeforeCheckIn) {
            return 0;
        }
        
        // Calculate additional percentage based on how close to check-in
        // The closer to check-in, the higher the discount
        $daysFromMax = $maxDaysBeforeCheckIn - $daysUntilCheckIn;
        $additionalPercent = $daysFromMax * $additionalPercentPerDayCloser;
        
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