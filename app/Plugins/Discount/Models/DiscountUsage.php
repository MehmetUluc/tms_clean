<?php

namespace App\Plugins\Discount\Models;

use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Core\src\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DiscountUsage extends BaseModel
{
    use HasFactory, HasTenant {
        HasTenant::bootHasTenant as baseBootHasTenant;
    }

    protected static function bootHasTenant()
    {
        // Tenant izolasyonu etkin değilse işlemi atla
        if (!config('core.tenant_isolation', true)) {
            return;
        }

        static::creating(function ($model) {
            if (!$model->isDirty('tenant_id') && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    protected $table = 'discount_usages';

    protected $fillable = [
        'discount_id',
        'discount_code_id',
        'user_id',
        'order_id',
        'amount',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the discount that was used
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the discount code that was used (if any)
     */
    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    /**
     * Get the user that used the discount
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the order that the discount was applied to
     * 
     * This can be a reservation or any other orderable entity
     */
    public function order(): MorphTo
    {
        return $this->morphTo();
    }
}