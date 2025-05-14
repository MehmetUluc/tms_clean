<?php

namespace App\Plugins\Accommodation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Plugins\Core\src\Models\BaseModel;

class HotelType extends BaseModel
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
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'hotel_type_id');
    }
}