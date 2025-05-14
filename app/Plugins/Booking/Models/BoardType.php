<?php

namespace App\Plugins\Booking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Pricing\Models\RoomRate;

class BoardType extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'created_by',
        'updated_by',
        'name',           // Pansiyon tipinin adı (örn: Herşey Dahil, Yarım Pansiyon)
        'code',           // Kısa kod (örn: AI, HB)
        'description',    // Açıklama
        'icon',           // İkon
        'includes',       // Dahil olan öğeler (JSON)
        'excludes',       // Hariç tutulan öğeler (JSON)
        'sort_order',     // Sıralama
        'is_active',      // Aktif mi?
        'notes',          // Ek notlar
    ];

    protected $casts = [
        'includes' => 'array',
        'excludes' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Bu pansiyon tipini kullanan oda tipleri ilişkisi (eski bağlantı)
     */
    public function roomTypes(): BelongsToMany
    {
        return $this->belongsToMany(RoomType::class, 'room_type_board_type')
                    ->withPivot('price_modifier')
                    ->withTimestamps();
    }
    
    // Pansiyon tipini kullanan odalar ilişkisi kaldırıldı - PricingV2 ile yeni mimari kullanılacak

    /**
     * Board type'a ait fiyat tarifeleri ilişkisi
     */
    public function rates(): HasMany
    {
        return $this->hasMany(RoomRate::class);
    }

    /**
     * Bu pansiyon tipini kullanan oteller
     */
    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(\App\Plugins\Accommodation\Models\Hotel::class, 'hotel_board_types')
                    ->withPivot(['pricing_calculation_method'])
                    ->withTimestamps();
    }
}