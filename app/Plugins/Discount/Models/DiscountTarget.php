<?php

namespace App\Plugins\Discount\Models;

use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Core\src\Traits\HasTenant;
use App\Plugins\Discount\Enums\TargetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DiscountTarget extends BaseModel
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

    protected $table = 'discount_targets';

    protected $fillable = [
        'discount_id',
        'target_type',
        'target_id',
    ];

    protected $casts = [
        'target_type' => TargetType::class,
    ];

    /**
     * Get the discount that owns this target
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Check if this target applies to the given entity
     */
    public function appliesTo(string $entityType, int $entityId = null): bool
    {
        // If target type is ALL, it applies to everything
        if ($this->target_type === TargetType::ALL) {
            return true;
        }
        
        // If entity type doesn't match target type, it doesn't apply
        if ($entityType !== $this->target_type->value) {
            return false;
        }
        
        // If target requires an ID but none provided or doesn't match, it doesn't apply
        if ($this->target_type->requiresTargetId() && ($entityId === null || $entityId != $this->target_id)) {
            return false;
        }
        
        return true;
    }
}