<?php

namespace App\Plugins\Accommodation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Pricing\Models\RoomRate;
use App\Plugins\Pricing\Models\RoomInventory;
use App\Plugins\Booking\Models\BoardType;

class RoomType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // 'tenant_id', // removed - doesn't exist in database
        // 'created_by', 
        // 'updated_by',
        'name',
        'slug',
        'description',
        'icon',
        'max_adults',
        'max_children',
        'max_guests',
        'is_active',
        'sort_order',
        'size',
        'bed_configuration',
        'view_type',
        'location',
        'features',
        'status',
    ];

    protected $casts = [
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'max_guests' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'size' => 'decimal:2',
        'bed_configuration' => 'array',
        'view_type' => 'array',
        'location' => 'array',
        'features' => 'array',
    ];

    /**
     * Odalar ilişkisi
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
    
    /**
     * Fiyat tarifeleri ilişkisi
     */
    public function rates(): HasMany
    {
        return $this->hasMany(RoomRate::class);
    }
    
    /**
     * Envanter ilişkisi
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(RoomInventory::class);
    }
    
    /**
     * Pansiyon tipleri ilişkisi
     */
    public function boardTypes(): BelongsToMany
    {
        return $this->belongsToMany(BoardType::class, 'room_type_board_type')
                    ->withPivot('price_modifier', 'is_default')
                    ->withTimestamps();
    }
}