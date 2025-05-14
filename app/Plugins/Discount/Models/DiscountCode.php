<?php

namespace App\Plugins\Discount\Models;

use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Core\src\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class DiscountCode extends BaseModel
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

    protected $table = 'discount_codes';

    protected $fillable = [
        'discount_id',
        'code',
        'max_uses',
        'used_count',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the discount that owns this code
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get all usages for this discount code
     */
    public function usages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }

    /**
     * Scope to get active discount codes
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
            })
            ->where(function ($query) {
                $query->where('max_uses', 0)
                    ->orWhereRaw('used_count < max_uses');
            });
    }

    /**
     * Check if the code is valid
     */
    public function isValid(): bool
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
        
        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses) {
            return false;
        }
        
        return true;
    }

    /**
     * Increment the used count
     */
    public function incrementUsedCount(): self
    {
        $this->increment('used_count');
        return $this;
    }
}