<?php

namespace App\Plugins\Pricing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Plugins\Accommodation\Models\Room;

class Inventory extends Model
{
    protected $table = 'inventories';
    
    protected $fillable = [
        'rate_plan_id',
        'room_id',
        'date',
        'available',       // Number of rooms available
        'total',           // Total capacity
        'is_closed',       // Closed for sales on this date
        'stop_sell',       // Temp stop sell flag
        'notes',
    ];
    
    protected $casts = [
        'date' => 'date',
        'available' => 'integer',
        'total' => 'integer',
        'is_closed' => 'boolean',
        'stop_sell' => 'boolean',
    ];
    
    /**
     * Get the rate plan that owns this inventory
     */
    public function ratePlan(): BelongsTo
    {
        return $this->belongsTo(RatePlan::class);
    }
    
    /**
     * Get the room that owns this inventory
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}