<?php

namespace App\Plugins\Discount\Models;

use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Core\src\Traits\HasTenant;
use App\Plugins\Discount\Enums\ConditionType;
use App\Plugins\Discount\Enums\ConditionOperator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCondition extends BaseModel
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

    protected $table = 'discount_conditions';

    protected $fillable = [
        'discount_id',
        'condition_type',
        'operator',
        'value',
    ];

    protected $casts = [
        'condition_type' => ConditionType::class,
        'operator' => ConditionOperator::class,
        'value' => 'array',
    ];

    /**
     * Get the discount that owns this condition
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Check if the condition is met for the given parameters
     */
    public function isMet(array $parameters): bool
    {
        $type = $this->condition_type;
        $operator = $this->operator;
        $value = $this->value;
        
        // The parameter value we're checking
        $paramValue = $parameters[$type->value] ?? null;
        
        if ($paramValue === null) {
            return false;
        }
        
        return match ($operator) {
            ConditionOperator::EQUALS => $paramValue == $value,
            ConditionOperator::NOT_EQUALS => $paramValue != $value,
            ConditionOperator::GREATER_THAN => $paramValue > $value,
            ConditionOperator::LESS_THAN => $paramValue < $value,
            ConditionOperator::GREATER_THAN_OR_EQUALS => $paramValue >= $value,
            ConditionOperator::LESS_THAN_OR_EQUALS => $paramValue <= $value,
            ConditionOperator::IN => in_array($paramValue, (array) $value),
            ConditionOperator::NOT_IN => !in_array($paramValue, (array) $value),
            ConditionOperator::CONTAINS => is_array($paramValue) && count(array_intersect($paramValue, (array) $value)) > 0,
            ConditionOperator::NOT_CONTAINS => !is_array($paramValue) || count(array_intersect($paramValue, (array) $value)) === 0,
            default => false,
        };
    }
}