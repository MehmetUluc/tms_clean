<?php

namespace App\Plugins\Amenities\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Accommodation\Models\Room;

class RoomAmenity extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'created_by',
        'updated_by',
        'name',
        'slug',
        'description',
        'icon',
        'category',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_room_amenity');
    }
}