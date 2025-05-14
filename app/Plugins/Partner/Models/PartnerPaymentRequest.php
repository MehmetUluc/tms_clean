<?php

namespace App\Plugins\Partner\Models;

use App\Models\User;
use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerPaymentRequest extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'partner_payment_requests';

    protected $fillable = [
        'partner_id',
        'bank_account_id',
        'amount',
        'currency',
        'requested_date',
        'status',
        'notes',
        'rejection_reason',
        'processed_date',
        'processed_by',
        'created_by',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_date' => 'datetime',
        'processed_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the partner that owns the payment request.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the bank account associated with the payment request.
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(PartnerBankAccount::class, 'bank_account_id');
    }

    /**
     * Get the user who processed the payment request.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the user who created the payment request.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the payment request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payment request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the payment request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the payment request is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if the payment request is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Approve the payment request.
     */
    public function approve(int $processedBy, string $notes = null): bool
    {
        $this->status = 'approved';
        $this->processed_by = $processedBy;
        $this->processed_date = now();
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Reject the payment request.
     */
    public function reject(int $processedBy, string $rejectionReason, string $notes = null): bool
    {
        $this->status = 'rejected';
        $this->processed_by = $processedBy;
        $this->processed_date = now();
        $this->rejection_reason = $rejectionReason;
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Mark the payment request as paid.
     */
    public function markAsPaid(int $processedBy, string $referenceNumber = null, string $notes = null): bool
    {
        $this->status = 'paid';
        $this->processed_by = $processedBy;
        $this->processed_date = now();
        
        if ($referenceNumber) {
            $this->reference_number = $referenceNumber;
        }
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Cancel the payment request.
     */
    public function cancel(int $processedBy, string $notes = null): bool
    {
        $this->status = 'cancelled';
        $this->processed_by = $processedBy;
        $this->processed_date = now();
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Generate a unique reference number.
     */
    public static function generateReferenceNumber(): string
    {
        return 'PMT-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }

    /**
     * Scope a query to only include payment requests with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to include payment requests within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('requested_date', [$startDate, $endDate]);
    }
}