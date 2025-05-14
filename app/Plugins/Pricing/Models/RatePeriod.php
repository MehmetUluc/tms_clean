<?php

namespace App\Plugins\Pricing\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RatePeriod extends Model
{
    protected $table = 'rate_periods';

    protected $fillable = [
        'rate_plan_id',
        'start_date',
        'end_date',
        'base_price',
        'prices',
        'min_stay',
        'quantity',
        'sales_type',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'base_price' => 'decimal:2',
        'prices' => 'json',
        'min_stay' => 'integer',
        'quantity' => 'integer',
        'sales_type' => 'string',
        'status' => 'boolean',
    ];

    /**
     * Get the rate plan that owns the rate period.
     */
    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }

    /**
     * Get the rate exceptions for the rate period.
     */
    public function rateExceptions(): HasMany
    {
        return $this->hasMany(RateException::class);
    }

    /**
     * Scope for getting periods that are active.
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', true);
    }

    /**
     * Scope for getting periods that contain the given date.
     */
    public function scopeContainsDate($query, $date): Builder
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
    }

    /**
     * Scope for getting periods that overlap with the given date range.
     */
    public function scopeOverlapping($query, $startDate, $endDate): Builder
    {
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }
        
        return $query->where(function($q) use ($startDate, $endDate) {
            // Period starts during the range
            $q->where(function($q1) use ($startDate, $endDate) {
                $q1->where('start_date', '>=', $startDate)
                   ->where('start_date', '<=', $endDate);
            })
            // Period ends during the range
            ->orWhere(function($q2) use ($startDate, $endDate) {
                $q2->where('end_date', '>=', $startDate)
                   ->where('end_date', '<=', $endDate);
            })
            // Period contains the range
            ->orWhere(function($q3) use ($startDate, $endDate) {
                $q3->where('start_date', '<=', $startDate)
                   ->where('end_date', '>=', $endDate);
            });
        });
    }

    /**
     * Get the price for a given occupancy
     * 
     * @param int $occupancy
     * @return float|null
     */
    public function getPriceForOccupancy(int $occupancy)
    {
        $ratePlan = $this->ratePlan;
        
        if (!$ratePlan->is_per_person) {
            // Ensure base_price is never null
            return $this->base_price ?? 0;
        }
        
        $prices = $this->prices ?? [];
        
        // Return price from prices array or default to base_price if not found
        // This ensures we always return a numeric value
        return $prices[$occupancy] ?? $this->base_price ?? 0;
    }

    /**
     * Check if period has any exceptions for the given date.
     * 
     * @param string|Carbon $date
     * @return bool
     */
    public function hasExceptionForDate($date): bool
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $this->rateExceptions()
                    ->where('date', $date->format('Y-m-d'))
                    ->exists();
    }

    /**
     * Get exception for a specific date, if it exists.
     * 
     * @param string|Carbon $date
     * @return RateException|null
     */
    public function getExceptionForDate($date)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return $this->rateExceptions()
                    ->where('date', $date->format('Y-m-d'))
                    ->first();
    }
}