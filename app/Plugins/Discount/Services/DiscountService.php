<?php

namespace App\Plugins\Discount\Services;

use App\Plugins\Discount\Contracts\DiscountServiceInterface;
use App\Plugins\Discount\Models\Discount;
use App\Plugins\Discount\Models\DiscountCode;
use App\Plugins\Discount\Models\DiscountUsage;
use App\Plugins\Discount\Enums\StackType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DiscountService implements DiscountServiceInterface
{
    /**
     * @var DiscountCalculatorFactory
     */
    protected $calculatorFactory;

    /**
     * Create a new DiscountService instance.
     *
     * @param DiscountCalculatorFactory $calculatorFactory
     */
    public function __construct(DiscountCalculatorFactory $calculatorFactory)
    {
        $this->calculatorFactory = $calculatorFactory;
    }

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
    ): array {
        // Start with active discounts
        $query = Discount::query()->active();

        // Find discounts that target this entity
        $query->whereHas('targets', function ($q) use ($entityType, $entityId) {
            $q->where(function ($sq) use ($entityType, $entityId) {
                // Target ALL
                $sq->where('target_type', 'ALL');
                
                // Target this entity type without a specific ID
                $sq->orWhere(function ($sqq) use ($entityType) {
                    $sqq->where('target_type', $entityType)
                        ->whereNull('target_id');
                });
                
                // Target this specific entity
                if ($entityId !== null) {
                    $sq->orWhere(function ($sqq) use ($entityType, $entityId) {
                        $sqq->where('target_type', $entityType)
                            ->where('target_id', $entityId);
                    });
                }
            });
        });

        // Include specific discount code if provided
        if ($discountCode) {
            $query->orWhereHas('codes', function ($q) use ($discountCode) {
                $q->where('code', $discountCode)
                    ->where('is_active', true)
                    ->where(function ($sq) {
                        $sq->where('max_uses', 0)
                            ->orWhereRaw('used_count < max_uses');
                    })
                    ->where(function ($sq) {
                        $sq->whereNull('start_date')
                            ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($sq) {
                        $sq->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    });
            });
        }

        // Get the discounts
        $discounts = $query->with(['conditions', 'targets', 'codes'])->get();

        // Filter discounts based on conditions
        return $discounts->filter(function ($discount) use ($context) {
            // If there are no conditions, the discount is applicable
            if ($discount->conditions->isEmpty()) {
                return true;
            }

            // Check all conditions
            foreach ($discount->conditions as $condition) {
                if (!$condition->isMet($context)) {
                    return false;
                }
            }

            return true;
        })->sortByDesc('priority')->values()->all();
    }

    /**
     * Calculate the discount amount for a given price
     *
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discount amount
     */
    public function calculateDiscountAmount(Discount $discount, float $price, array $context = []): float
    {
        // Skip if price is below minimum booking value
        if ($discount->min_booking_value && $price < $discount->min_booking_value) {
            return 0;
        }

        // Get the appropriate calculator for this discount type
        $calculator = $this->calculatorFactory->createCalculator($discount->discount_type);
        
        // Calculate the discount amount
        $amount = $calculator->calculate($discount, $price, $context);
        
        // Apply max value limit if set
        if ($discount->max_value && $amount > $discount->max_value) {
            $amount = $discount->max_value;
        }
        
        // Make sure discount doesn't exceed price
        if ($amount > $price) {
            $amount = $price;
        }
        
        return $amount;
    }

    /**
     * Apply a discount to a price
     *
     * @param Discount $discount The discount to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return float The discounted price
     */
    public function applyDiscount(Discount $discount, float $price, array $context = []): float
    {
        $discountAmount = $this->calculateDiscountAmount($discount, $price, $context);
        return $price - $discountAmount;
    }

    /**
     * Apply the best discount to a price
     *
     * @param array $discounts An array of discounts to consider
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return array [float $discountedPrice, Discount $appliedDiscount]
     */
    public function applyBestDiscount(array $discounts, float $price, array $context = []): array
    {
        if (empty($discounts)) {
            return [$price, null];
        }

        $bestDiscount = null;
        $bestDiscountPrice = $price;

        foreach ($discounts as $discount) {
            $discountedPrice = $this->applyDiscount($discount, $price, $context);
            
            if ($discountedPrice < $bestDiscountPrice) {
                $bestDiscountPrice = $discountedPrice;
                $bestDiscount = $discount;
            }
        }

        return [$bestDiscountPrice, $bestDiscount];
    }

    /**
     * Apply multiple stackable discounts to a price
     *
     * @param array $discounts An array of stackable discounts to apply
     * @param float $price The original price
     * @param array $context Additional context for discount calculation
     * @return array [float $discountedPrice, array $appliedDiscounts]
     */
    public function applyStackableDiscounts(array $discounts, float $price, array $context = []): array
    {
        $discountedPrice = $price;
        $appliedDiscounts = [];

        // Get exclusive discounts (highest priority first)
        $exclusiveDiscounts = collect($discounts)
            ->filter(function ($discount) {
                return $discount->stack_type === StackType::EXCLUSIVE;
            })
            ->sortByDesc('priority')
            ->values()
            ->all();

        // Get stackable discounts (highest priority first)
        $stackableDiscounts = collect($discounts)
            ->filter(function ($discount) {
                return $discount->stack_type === StackType::STACKABLE;
            })
            ->sortByDesc('priority')
            ->values()
            ->all();

        // If there are exclusive discounts, use the best one
        if (!empty($exclusiveDiscounts)) {
            [$discountedPrice, $bestExclusiveDiscount] = $this->applyBestDiscount($exclusiveDiscounts, $price, $context);
            
            if ($bestExclusiveDiscount) {
                $appliedDiscounts[] = $bestExclusiveDiscount;
                return [$discountedPrice, $appliedDiscounts];
            }
        }

        // Otherwise, stack all stackable discounts
        $currentPrice = $price;
        foreach ($stackableDiscounts as $discount) {
            $newPrice = $this->applyDiscount($discount, $currentPrice, $context);
            
            if ($newPrice < $currentPrice) {
                $appliedDiscounts[] = $discount;
                $currentPrice = $newPrice;
            }
        }

        return [$currentPrice, $appliedDiscounts];
    }

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
    ): bool {
        try {
            // Find discount code if provided
            $discountCodeModel = null;
            if ($discountCode) {
                $discountCodeModel = DiscountCode::where('code', $discountCode)
                    ->where('discount_id', $discount->id)
                    ->first();
                
                // If discount code exists, increment its used count
                if ($discountCodeModel) {
                    $discountCodeModel->incrementUsedCount();
                }
            }

            // Create usage record
            DiscountUsage::create([
                'tenant_id' => $discount->tenant_id,
                'discount_id' => $discount->id,
                'discount_code_id' => $discountCodeModel ? $discountCodeModel->id : null,
                'user_id' => $userId,
                'order_type' => $orderType,
                'order_id' => $orderId,
                'amount' => $amount,
                'metadata' => $metadata,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to record discount usage: ' . $e->getMessage(), [
                'discount_id' => $discount->id,
                'order_type' => $orderType,
                'order_id' => $orderId,
                'amount' => $amount,
                'user_id' => $userId,
                'discount_code' => $discountCode,
            ]);
            
            return false;
        }
    }

    /**
     * Validate a discount code
     *
     * @param string $code The discount code to validate
     * @return Discount|null The discount if valid, null otherwise
     */
    public function validateDiscountCode(string $code): ?Discount
    {
        $discountCode = DiscountCode::where('code', $code)
            ->active()
            ->with('discount')
            ->first();
        
        if (!$discountCode) {
            return null;
        }
        
        $discount = $discountCode->discount;
        
        // Check if discount is active
        if (!$discount->isActive()) {
            return null;
        }
        
        // Check if discount has reached total usage limit
        if ($discount->hasReachedUsageLimit()) {
            return null;
        }
        
        return $discount;
    }
}