<?php

namespace App\Plugins\Pricing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyRate extends Model
{
    protected $table = 'daily_rates';
    
    protected $fillable = [
        'rate_plan_id',
        'date',
        'base_price',       // For unit-based pricing
        'currency',
        'is_closed',        // If true, no reservations can be made for this day
        'min_stay_arrival', // Minimum stay if arrival is on this day
        'status',           // available, limited, sold_out
        'notes',
        'is_per_person',    // true=per person pricing, false=unit pricing
        'prices_json',      // JSON storing prices for different occupancy levels
        'is_refundable',    // true=refundable, false=non-refundable
        'sales_type',       // direct or ask_sell (Sor-Sat)
    ];
    
    protected $casts = [
        'date' => 'date',
        'base_price' => 'decimal:2',
        'is_closed' => 'boolean',
        'min_stay_arrival' => 'integer',
        'is_per_person' => 'boolean',
        'prices_json' => 'json',
        'is_refundable' => 'boolean',
    ];
    
    /**
     * Get the rate plan that owns this daily rate
     */
    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }

    /**
     * Get price for a specific occupancy
     *
     * @param int $occupancy
     * @return float|null
     */
    public function getPriceForOccupancy(int $occupancy): ?float
    {
        // If not per person pricing, just return base price
        if (!$this->is_per_person) {
            return $this->base_price;
        }

        // If per person pricing, check prices_json
        if (!$this->prices_json) {
            return $this->base_price; // Fallback to base price if no JSON data
        }

        $prices = $this->prices_json;
        $occupancyKey = (string)$occupancy;

        // Return specific price if available, otherwise return base_price
        return $prices[$occupancyKey] ?? $this->base_price;
    }

    /**
     * Get non-refundable price
     * This applies any configured discount for non-refundable rates
     *
     * @param float|null $discount Discount percentage (0-100)
     * @return float
     */
    public function getNonRefundablePrice(?float $discount = null): float
    {
        // If already non-refundable, just return the base price
        if (!$this->is_refundable) {
            return $this->base_price;
        }

        // If no discount specified, use default (e.g., 10%)
        $discountRate = $discount ?? 10;

        // Apply discount to base price
        return round($this->base_price * (1 - ($discountRate / 100)), 2);
    }

    /**
     * Get all prices for different occupancy levels
     *
     * @return array
     */
    public function getAllPrices(): array
    {
        if (!$this->is_per_person) {
            return ['unit' => $this->base_price];
        }

        if ($this->prices_json) {
            return $this->prices_json;
        }

        // Fallback: return base price as single person price
        return ['1' => $this->base_price];
    }

    /**
     * Get pricing type label
     *
     * @return string
     */
    public function getPricingTypeLabel(): string
    {
        return $this->is_per_person ? 'Kişi Başı' : 'Oda Başı';
    }

    /**
     * Get refund type label
     *
     * @return string
     */
    public function getRefundTypeLabel(): string
    {
        return $this->is_refundable ? 'İade Edilebilir' : 'İade Edilemez';
    }
}