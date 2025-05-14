<?php

namespace App\Plugins\Discount\Services\Calculators;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Models\Discount;

class PackageDealDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for a package deal discount
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculate(Discount $discount, float $price, array $context = []): float
    {
        // The value field contains the percentage discount for the package
        $percentage = $discount->value;
        
        // Get the configuration values
        $config = $discount->configuration ?? [];
        $requiredServices = $config['required_services'] ?? [];
        
        // Get the booked services from context
        $bookedServices = $context['booked_services'] ?? [];
        
        // If no required services defined or no booked services provided, no discount
        if (empty($requiredServices) || empty($bookedServices)) {
            return 0;
        }
        
        // Check if all required services are booked
        $allServicesBooked = true;
        foreach ($requiredServices as $service) {
            if (!in_array($service, $bookedServices)) {
                $allServicesBooked = false;
                break;
            }
        }
        
        // If not all required services are booked, no discount
        if (!$allServicesBooked) {
            return 0;
        }
        
        // Calculate discount amount
        $discountAmount = ($percentage / 100) * $price;
        
        // If there's a max value, apply it
        if ($discount->max_value && $discountAmount > $discount->max_value) {
            $discountAmount = $discount->max_value;
        }
        
        // Ensure the discount doesn't exceed the original price
        return min($discountAmount, $price);
    }
}