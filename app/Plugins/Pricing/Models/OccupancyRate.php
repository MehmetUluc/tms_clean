<?php

namespace App\Plugins\Pricing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccupancyRate extends Model
{
    protected $table = 'occupancy_rates';
    
    protected $fillable = [
        'rate_plan_id',
        'date',            // Can be null for default pricing
        'occupancy',       // Number of people (1, 2, 3, etc.)
        'price',           // Price for this occupancy
        'currency',
        'is_default',      // If true, this price is used when no date-specific price exists
    ];
    
    protected $casts = [
        'date' => 'date',
        'occupancy' => 'integer',
        'price' => 'decimal:2',
        'is_default' => 'boolean',
    ];
    
    /**
     * Get the rate plan that owns this occupancy rate
     */
    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }
}