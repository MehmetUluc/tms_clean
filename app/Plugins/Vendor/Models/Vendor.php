<?php

namespace App\Plugins\Vendor\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Plugins\Accommodation\Models\Hotel;

class Vendor extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendors';

    protected $fillable = [
        'user_id',
        'company_name',
        'tax_number',
        'tax_office',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'website',
        'contact_person',
        'contact_email',
        'contact_phone',
        'status',
        'default_commission_rate',
        'contract_start_date',
        'contract_end_date',
        'notes',
    ];

    protected $casts = [
        'default_commission_rate' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user associated with the vendor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hotels associated with the vendor.
     */
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    /**
     * Get the bank accounts associated with the vendor.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(VendorBankAccount::class);
    }

    /**
     * Get the documents associated with the vendor.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VendorDocument::class);
    }

    /**
     * Get the commissions associated with the vendor.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(VendorCommission::class);
    }

    /**
     * Get the payments associated with the vendor.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(VendorPayment::class);
    }

    /**
     * Get the payment requests associated with the vendor.
     */
    public function paymentRequests(): HasMany
    {
        return $this->hasMany(VendorPaymentRequest::class);
    }

    /**
     * Get the transactions associated with the vendor.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(VendorTransaction::class);
    }

    /**
     * Get the ministry reports associated with the vendor.
     */
    public function ministryReports(): HasMany
    {
        return $this->hasMany(VendorMinistryReport::class);
    }

    /**
     * Check if vendor is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Calculate the commission amount based on the given amount.
     */
    public function calculateCommission(float $amount): float
    {
        return $amount * ($this->default_commission_rate / 100);
    }

    /**
     * Calculate the net amount after commission.
     */
    public function calculateNetAmount(float $amount): float
    {
        return $amount - $this->calculateCommission($amount);
    }
}