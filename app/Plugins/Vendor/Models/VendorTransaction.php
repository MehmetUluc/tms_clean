<?php

namespace App\Plugins\Vendor\Models;

use App\Models\User;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorTransaction extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_transactions';

    protected $fillable = [
        'vendor_id',
        'reservation_id',
        'hotel_id',
        'amount',
        'commission_amount',
        'net_amount',
        'currency',
        'transaction_date',
        'transaction_type',
        'status',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the vendor that owns the transaction.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the reservation associated with the transaction.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the hotel associated with the transaction.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the user who created the transaction.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate a unique reference number.
     */
    public static function generateReferenceNumber(): string
    {
        return 'TRX-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }

    /**
     * Scope a query to only include transactions with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include transactions of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope a query to include transactions within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include transactions for a specific hotel.
     */
    public function scopeForHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transaction is processed.
     */
    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    /**
     * Check if the transaction is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the transaction is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark the transaction as processed.
     */
    public function markAsProcessed(): bool
    {
        $this->status = 'processed';
        return $this->save();
    }

    /**
     * Mark the transaction as cancelled.
     */
    public function markAsCancelled(string $notes = null): bool
    {
        $this->status = 'cancelled';
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Mark the transaction as failed.
     */
    public function markAsFailed(string $notes = null): bool
    {
        $this->status = 'failed';
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }
}