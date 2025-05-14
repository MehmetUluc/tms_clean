<?php

namespace App\Plugins\Partner\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerBankAccount extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'partner_bank_accounts';

    protected $fillable = [
        'partner_id',
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
     * Get the partner that owns the bank account.
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
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