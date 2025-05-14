<?php

namespace App\Plugins\Pricing\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateException extends Model
{
    protected $table = 'rate_exceptions';

    protected $fillable = [
        'rate_period_id',
        'date',
        'base_price',
        'prices',
        'min_stay',
        'quantity',
        'sales_type',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'base_price' => 'decimal:2',
        'prices' => 'json',
        'min_stay' => 'integer',
        'quantity' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the rate period that owns the rate exception.
     */
    public function ratePeriod(): BelongsTo
    {
        return $this->belongsTo(RatePeriod::class);
    }

    /**
     * Get the rate plan through rate period.
     */
    public function getRatePlanAttribute()
    {
        return $this->ratePeriod->ratePlan;
    }

    /**
     * Scope for getting exceptions that are active.
     * If status is null, it falls back to the parent period's status.
     */
    public function scopeActive($query): Builder
    {
        return $query->where(function($q) {
            $q->where('status', true)
              ->orWhereHas('ratePeriod', function($q) {
                  $q->where('status', true);
              });
        });
    }

    /**
     * Scope for getting exceptions on a specific date.
     */
    public function scopeOnDate($query, $date): Builder
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $query->where('date', $date->format('Y-m-d'));
    }

    /**
     * Get the effective price for a given occupancy, falling back to period if needed
     * 
     * @param int $occupancy
     * @return float|null
     */
    public function getEffectivePriceForOccupancy(int $occupancy)
    {
        $ratePlan = $this->ratePlan;
        
        if (!$ratePlan->is_per_person) {
            return $this->base_price ?? $this->ratePeriod->base_price;
        }
        
        $prices = $this->prices ?? [];
        
        // If exception has price for this occupancy, use it
        if (isset($prices[$occupancy])) {
            return $prices[$occupancy];
        }
        
        // Otherwise fall back to rate period's price
        $periodPrices = $this->ratePeriod->prices ?? [];
        return $periodPrices[$occupancy] ?? null;
    }

    /**
     * Get the effective min_stay, falling back to period if needed
     * 
     * @return int
     */
    public function getEffectiveMinStay(): int
    {
        return $this->min_stay ?? $this->ratePeriod->min_stay;
    }

    /**
     * Get the effective quantity, falling back to period if needed
     * 
     * @return int
     */
    public function getEffectiveQuantity(): int
    {
        return $this->quantity ?? $this->ratePeriod->quantity;
    }

    /**
     * Get the effective sales_type, falling back to period if needed
     * 
     * @return string
     */
    public function getEffectiveSalesType(): string
    {
        return $this->sales_type ?? $this->ratePeriod->sales_type;
    }

    /**
     * Get the effective status, falling back to period if needed
     * 
     * @return bool
     */
    public function getEffectiveStatus(): bool
    {
        return $this->status ?? $this->ratePeriod->status;
    }
}