<?php

namespace App\Plugins\Pricing\Models;

use App\Plugins\Booking\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPrice extends Model
{
    protected $table = 'booking_prices';

    protected $fillable = [
        'reservation_id',
        'rate_plan_id',
        'date',
        'price',
        'guests_count',
        'is_per_person',
        'original_data',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'guests_count' => 'integer',
        'is_per_person' => 'boolean',
        'original_data' => 'json',
    ];

    /**
     * Get the reservation that owns the booking price.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the rate plan that owns the booking price.
     */
    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }

    /**
     * Scope for getting prices for a specific reservation.
     */
    public function scopeForReservation($query, $reservationId): Builder
    {
        return $query->where('reservation_id', $reservationId);
    }

    /**
     * Scope for getting prices on a specific date.
     */
    public function scopeOnDate($query, $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope for getting prices within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate): Builder
    {
        return $query->whereDate('date', '>=', $startDate)
                     ->whereDate('date', '<=', $endDate);
    }

    /**
     * Calculate total price based on is_per_person flag
     * 
     * @return float
     */
    public function getTotalPriceAttribute(): float
    {
        if ($this->is_per_person) {
            return $this->price * $this->guests_count;
        }
        
        return $this->price;
    }
}