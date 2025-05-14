<?php

namespace App\Plugins\Pricing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildPolicy extends Model
{
    protected $table = 'child_policies';
    
    protected $fillable = [
        'rate_plan_id',
        'min_age',        // Minimum age for this policy (e.g., 0 for infants)
        'max_age',        // Maximum age for this policy (e.g., 6 for young children)
        'policy_type',    // free, fixed_price, percentage
        'amount',         // Price or percentage value, depending on policy_type
        'currency',       // Only used for fixed_price
        'max_children',   // Maximum number of children allowed with this policy
        'child_number',   // 1 for first child, 2 for second child, etc.
    ];
    
    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'amount' => 'decimal:2',
        'max_children' => 'integer',
        'child_number' => 'integer',
    ];
    
    /**
     * Get the rate plan that owns this child policy
     */
    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }
}