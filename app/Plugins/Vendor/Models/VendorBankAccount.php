<?php

namespace App\Plugins\Vendor\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorBankAccount extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendor_bank_accounts';

    protected $fillable = [
        'vendor_id',
        'bank_name',
        'account_name',
        'iban',
        'account_number',
        'branch_code',
        'swift_code',
        'currency',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the vendor that owns the bank account.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Mask the IBAN for display.
     */
    public function getMaskedIbanAttribute(): string
    {
        if (empty($this->iban)) {
            return '';
        }

        $length = strlen($this->iban);
        if ($length <= 8) {
            return $this->iban;
        }

        return substr($this->iban, 0, 4) . str_repeat('*', $length - 8) . substr($this->iban, -4);
    }
}