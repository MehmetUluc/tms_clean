<?php

namespace App\Plugins\Pricing\Models;

use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RatePlan extends Model
{
    protected $table = 'rate_plans';

    protected $fillable = [
        'hotel_id',
        'room_id',
        'board_type_id',
        'is_per_person',
        'status',
    ];

    protected $casts = [
        'is_per_person' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Get the hotel that owns the rate plan.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the room that owns the rate plan.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the board type that owns the rate plan.
     */
    public function boardType(): BelongsTo
    {
        return $this->belongsTo(BoardType::class);
    }

    /**
     * Get the rate periods for the rate plan.
     */
    public function ratePeriods(): HasMany
    {
        return $this->hasMany(RatePeriod::class);
    }

    /**
     * Get the daily rates for the rate plan.
     */
    public function dailyRates(): HasMany
    {
        return $this->hasMany(DailyRate::class);
    }

    /**
     * Active rate plans.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get plan identifier for display.
     */
    public function getIdentifierAttribute(): string
    {
        return "{$this->room->name} - {$this->boardType->name} - " . 
               ($this->is_per_person ? 'Kişi Bazlı' : 'Ünite Bazlı');
    }
}