<?php

namespace App\Plugins\Partner\Models;

use App\Models\User;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerCommission extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'partner_commissions';

    protected $fillable = [
        'partner_id',
        'hotel_id',
        'room_type_id',
        'board_type_id',
        'commission_rate',
        'start_date',
        'end_date',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the partner that owns the commission rate.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the hotel associated with the commission rate.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the room type associated with the commission rate.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the board type associated with the commission rate.
     */
    public function boardType(): BelongsTo
    {
        return $this->belongsTo(BoardType::class);
    }

    /**
     * Get the user who created the commission rate.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the commission rate.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Check if the commission is currently active.
     */
    public function isActive(): bool
    {
        $now = now()->startOfDay();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the commission amount based on the given amount.
     */
    public function calculateCommission(float $amount): float
    {
        return $amount * ($this->commission_rate / 100);
    }

    /**
     * Scope a query to only include active commissions.
     */
    public function scopeActive($query)
    {
        $now = now()->startOfDay();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_date')
                ->orWhere('start_date', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', $now);
        });
    }

    /**
     * Scope a query to find the applicable commission for a reservation.
     */
    public function scopeApplicable($query, $partnerId, $hotelId, $roomTypeId = null, $boardTypeId = null)
    {
        $query->where('partner_id', $partnerId)
            ->active()
            ->orderBy('start_date', 'desc'); // Most recent first

        // Filter by hotel, room type and board type with fallbacks
        return $query->where(function ($q) use ($hotelId, $roomTypeId, $boardTypeId) {
            // Most specific first: hotel + room type + board type
            if ($roomTypeId && $boardTypeId) {
                $q->orWhere(function ($q2) use ($hotelId, $roomTypeId, $boardTypeId) {
                    $q2->where('hotel_id', $hotelId)
                        ->where('room_type_id', $roomTypeId)
                        ->where('board_type_id', $boardTypeId);
                });
            }

            // Hotel + room type
            if ($roomTypeId) {
                $q->orWhere(function ($q2) use ($hotelId, $roomTypeId) {
                    $q2->where('hotel_id', $hotelId)
                        ->where('room_type_id', $roomTypeId)
                        ->whereNull('board_type_id');
                });
            }

            // Hotel + board type
            if ($boardTypeId) {
                $q->orWhere(function ($q2) use ($hotelId, $boardTypeId) {
                    $q2->where('hotel_id', $hotelId)
                        ->whereNull('room_type_id')
                        ->where('board_type_id', $boardTypeId);
                });
            }

            // Hotel only
            $q->orWhere(function ($q2) use ($hotelId) {
                $q2->where('hotel_id', $hotelId)
                    ->whereNull('room_type_id')
                    ->whereNull('board_type_id');
            });

            // Default partner rate (applies to all hotels)
            $q->orWhere(function ($q2) {
                $q2->whereNull('hotel_id')
                    ->whereNull('room_type_id')
                    ->whereNull('board_type_id');
            });
        });
    }
}