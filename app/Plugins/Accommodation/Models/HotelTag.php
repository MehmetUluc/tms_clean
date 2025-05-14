<?php

namespace App\Plugins\Accommodation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Plugins\Core\src\Models\BaseModel;

class HotelTag extends BaseModel
{
    use HasFactory;

    /**
     * Kitle atamasına izin verilen özellikler.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'created_by',
        'updated_by',
        'name',
        'slug',
        'description',
        'type',
        'icon',
        'is_active',
        'is_featured',
    ];

    /**
     * Tip dönüşümleri.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Bu etikete sahip oteller ilişkisi.
     */
    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_hotel_tag');
    }
}