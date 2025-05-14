<?php

namespace App\Plugins\Discount\Services\Calculators;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Models\Discount;

class FixedAmountDiscountCalculator implements DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for a fixed amount discount
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculate(Discount $discount, float $price, array $context = []): float
    {
        // The value field contains the fixed amount to discount
        $amount = $discount->value;
        
        // Ensure the discount doesn't exceed the original price
        return min($amount, $price);
    }
}