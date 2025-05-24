<?php

namespace App\Plugins\Partner\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Plugins\Accommodation\Models\Hotel;

class Partner extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'partners';

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
        'onboarding_completed',
        'onboarding_completed_at',
        'agreement_accepted',
        'agreement_accepted_at',
        'tourism_certificate_number',
        'tourism_certificate_valid_until',
        'staff_user_ids',
    ];

    protected $casts = [
        'default_commission_rate' => 'decimal:2',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'onboarding_completed' => 'boolean',
        'onboarding_completed_at' => 'datetime',
        'agreement_accepted' => 'boolean',
        'agreement_accepted_at' => 'datetime',
        'tourism_certificate_valid_until' => 'date',
        'staff_user_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user associated with the partner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hotels associated with the partner.
     */
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'partner_id');
    }

    /**
     * Get the bank accounts associated with the partner.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(PartnerBankAccount::class);
    }

    /**
     * Get the documents associated with the partner.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PartnerDocument::class);
    }

    /**
     * Get the commissions associated with the partner.
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(PartnerCommission::class);
    }

    /**
     * Get the payments associated with the partner.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PartnerPayment::class);
    }

    /**
     * Get the payment requests associated with the partner.
     */
    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PartnerPaymentRequest::class);
    }

    /**
     * Get the transactions associated with the partner.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PartnerTransaction::class);
    }

    /**
     * Get the ministry reports associated with the partner.
     */
    public function ministryReports(): HasMany
    {
        return $this->hasMany(PartnerMinistryReport::class);
    }

    /**
     * Check if partner is active.
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
    
    /**
     * Get staff users associated with this partner.
     */
    public function staff()
    {
        return $this->belongsToMany(User::class, 'partner_staff', 'partner_id', 'user_id')
                    ->withTimestamps();
    }
    
    /**
     * Check if user is staff member of this partner.
     */
    public function hasStaffMember(User $user): bool
    {
        return in_array($user->id, $this->staff_user_ids ?? []);
    }
    
    /**
     * Complete the onboarding process.
     */
    public function completeOnboarding(): void
    {
        $this->update([
            'onboarding_completed' => true,
            'onboarding_completed_at' => now(),
        ]);
    }
    
    /**
     * Accept the agreement.
     */
    public function acceptAgreement(): void
    {
        $this->update([
            'agreement_accepted' => true,
            'agreement_accepted_at' => now(),
        ]);
    }
}