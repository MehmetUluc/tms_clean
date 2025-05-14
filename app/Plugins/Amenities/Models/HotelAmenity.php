<?php

namespace App\Plugins\Amenities\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Accommodation\Models\Hotel;

class HotelAmenity extends BaseModel
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

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_hotel_amenity');
    }
}