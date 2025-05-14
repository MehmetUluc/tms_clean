<?php

namespace App\Plugins\Discount\Models;

use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Core\src\Traits\HasTenant;
use App\Plugins\Discount\Enums\DiscountType;
use App\Plugins\Discount\Enums\StackType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class Discount extends BaseModel
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

    protected $table = 'discounts';

    protected $fillable = [
        'name',
        'description',
        'discount_type', 
        'value',
        'max_value',
        'start_date',
        'end_date',
        'is_active',
        'priority',
        'stack_type',
        'min_booking_value',
        'max_uses_total',
        'max_uses_per_user',
        'configuration',
    ];

    protected $casts = [
        'discount_type' => DiscountType::class,
        'stack_type' => StackType::class,
        'value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'min_booking_value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'max_uses_total' => 'integer',
        'max_uses_per_user' => 'integer',
        'configuration' => 'array',
    ];

    /**
     * Get all conditions for this discount
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(DiscountCondition::class);
    }

    /**
     * Get all targets for this discount
     */
    public function targets(): HasMany
    {
        return $this->hasMany(DiscountTarget::class);
    }

    /**
     * Get all discount codes for this discount (if any)
     */
    public function codes(): HasMany
    {
        return $this->hasMany(DiscountCode::class);
    }

    /**
     * Get all usages for this discount
     */
    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    /**
     * Scope to get active discounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope to get exclusive discounts
     */
    public function scopeExclusive($query)
    {
        return $query->where('stack_type', StackType::EXCLUSIVE);
    }

    /**
     * Scope to get stackable discounts
     */
    public function scopeStackable($query)
    {
        return $query->where('stack_type', StackType::STACKABLE);
    }

    /**
     * Check if the discount is active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->start_date && $this->start_date > $now) {
            return false;
        }
        
        if ($this->end_date && $this->end_date < $now) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if discount has reached usage limit
     */
    public function hasReachedUsageLimit(): bool
    {
        if (!$this->max_uses_total) {
            return false;
        }

        return $this->usages()->count() >= $this->max_uses_total;
    }
}