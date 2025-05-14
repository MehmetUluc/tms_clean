<?php

namespace App\Plugins\Partner\Models;

use App\Models\User;
use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PartnerPayment extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'partner_payments';

    protected $fillable = [
        'partner_id',
        'payment_request_id',
        'bank_account_id',
        'amount',
        'currency',
        'payment_date',
        'due_date',
        'status',
        'payment_method',
        'payment_reference',
        'invoice_number',
        'receipt_file_path',
        'notes',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the partner that owns the payment.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the payment request associated with the payment.
     */
    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PartnerPaymentRequest::class, 'payment_request_id');
    }

    /**
     * Get the bank account associated with the payment.
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(PartnerBankAccount::class, 'bank_account_id');
    }

    /**
     * Get the user who created the payment.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the payment.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the URL to the receipt file.
     */
    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_file_path ? Storage::url($this->receipt_file_path) : null;
    }

    /**
     * Check if the payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the payment is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the payment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Mark the payment as completed.
     */
    public function markAsCompleted(int $approvedBy = null, string $paymentReference = null, string $notes = null): bool
    {
        $this->status = 'completed';
        
        if ($approvedBy) {
            $this->approved_by = $approvedBy;
        }
        
        if ($paymentReference) {
            $this->payment_reference = $paymentReference;
        }
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        $result = $this->save();
        
        // If this payment is associated with a payment request, mark it as paid
        if ($result && $this->payment_request_id) {
            $this->paymentRequest->markAsPaid(
                $approvedBy ?? auth()->id(),
                $paymentReference ?? $this->payment_reference,
                $notes ?? null
            );
        }
        
        return $result;
    }

    /**
     * Mark the payment as cancelled.
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
     * Mark the payment as failed.
     */
    public function markAsFailed(string $notes = null): bool
    {
        $this->status = 'failed';
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }

    /**
     * Generate a unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Scope a query to only include payments with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to include payments within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include overdue payments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }
}