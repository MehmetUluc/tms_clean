<?php

namespace App\Plugins\Discount\Contracts;

use App\Plugins\Discount\Models\Discount;

interface DiscountServiceInterface
{
    /**
     * Find applicable discounts for the given entity type and ID
     * 
     * @param string $entityType The type of entity to find discounts for (e.g., 'hotel', 'room')
     * @param int|null $entityId The ID of the entity (optional)
     * @param array $context Additional context for discount conditions (e.g., dates, guest count)
     * @param string|null $discountCode Optional discount code to include
     * @return array An array of applicable Discount models
     */
    public function findApplicableDiscounts(
        string $entityType, 
        ?int $entityId = null, 
        array $context = [], 
        ?string $discountCode = null
    ): array;
    
    /**
     * Calculate the discount amount for a given price
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculateDiscountAmount(Discount $discount, float $price, array $context = []): float;
    
    /**
     * Apply a discount to a price
     * 
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discounted price
     */
    public function applyDiscount(Discount $discount, float $price, array $context = []): float;
    
    /**
     * Apply the best discount to a price
     * 
     * @param array $discounts An array of discounts to consider
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return array [float $discountedPrice, Discount $appliedDiscount]
     */
    public function applyBestDiscount(array $discounts, float $price, array $context = []): array;
    
    /**
     * Apply multiple stackable discounts to a price
     * 
     * @param array $discounts An array of stackable discounts to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return array [float $discountedPrice, array $appliedDiscounts]
     */
    public function applyStackableDiscounts(array $discounts, float $price, array $context = []): array;
    
    /**
     * Record the usage of a discount
     * 
     * @param Discount $discount The discount that was used
     * @param string $orderType The type of order (e.g., 'reservation')
     * @param int $orderId The ID of the order
     * @param float $amount The amount that was discounted
     * @param int|null $userId The ID of the user (optional)
     * @param string|null $discountCode The discount code that was used (optional)
     * @param array $metadata Additional metadata about the discount usage
     * @return bool Whether the usage was recorded successfully
     */
    public function recordUsage(
        Discount $discount, 
        string $orderType, 
        int $orderId, 
        float $amount, 
        ?int $userId = null, 
        ?string $discountCode = null, 
        array $metadata = []
    ): bool;
    
    /**
     * Validate a discount code
     * 
     * @param string $code The discount code to validate
     * @return Discount|null The discount if valid, null otherwise
     */
    public function validateDiscountCode(string $code): ?Discount;
}