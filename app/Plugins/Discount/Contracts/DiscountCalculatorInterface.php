<?php

namespace App\Plugins\Discount\Contracts;

use App\Plugins\Discount\Models\Discount;

interface DiscountCalculatorInterface
{
    /**
     * Calculate the discount amount for a given price
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculate(Discount $discount, float $price, array $context = []): float;
}