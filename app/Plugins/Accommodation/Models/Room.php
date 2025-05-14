<?php

namespace App\Plugins\Accommodation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Accommodation\Scopes\VendorRoomScope;
use App\Plugins\Amenities\Models\RoomAmenity;
use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Booking\Models\Guest;
use App\Plugins\Pricing\Models\RoomRate;
use App\Plugins\Pricing\Models\PriceRule;
use App\Plugins\Pricing\Models\RoomInventory;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Vendor scope'u uygula - odalar için özel bir scope kullanıyoruz
        static::addGlobalScope(new VendorRoomScope);
    }

    protected $fillable = [
        // 'tenant_id', // removed - doesn't exist in database
        'hotel_id',  // Odanın hangi otele ait olduğu (gerekli)
        'room_type_id', // Oda tipi ilişkisi (gerekli)
        'name', // Odanın adı, örn: "101 nolu oda" (gerekli)
        'slug', // URL için slug
        'description', // Oda açıklaması (isteğe bağlı)
        'features_details', // Oda özelliklerinin detayları (JSON)
        'room_number', // Oda numarası (isteğe bağlı, kimlik amaçlı)
        'floor', // Kat bilgisi (isteğe bağlı)
        'capacity_adults', // Yetişkin kapasitesi (genellikle oda tipinden gelir)
        'capacity_children', // Çocuk kapasitesi (genellikle oda tipinden gelir)
        'size', // Metrekare (genellikle oda tipinden gelir)
        'base_price', // NOT: Bu sadece referans fiyatıdır. Gerçek fiyatlar RoomRate tablosundan gelir
        'pricing_calculation_method', // Fiyatlandırma hesaplama metodu: per_person (kişi başı) veya per_room (oda başı/unit)
        'child_policies', // Çocuk yaş ve fiyat politikaları (JSON)
        'is_active', // Oda aktif mi?
        'is_available', // Oda müsait mi? NOT: Gerçek müsaitlik RoomInventory tablosundan kontrol edilir
        'is_featured', // Öne çıkan oda mı?
        'cover_image', // Kapak resmi
        'gallery', // Galeri resimleri (JSON)
        'created_by', // Oluşturan kullanıcı
        'updated_by', // Güncelleyen kullanıcı
    ];

    protected $casts = [
        'capacity_adults' => 'integer',
        'capacity_children' => 'integer',
        'size' => 'decimal:2',
        'base_price' => 'decimal:2',
        'child_policies' => 'array',
        'features_details' => 'array',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'gallery' => 'array',
        'pricing_calculation_method' => 'string',
    ];

    /**
     * Kapak resmi URL'si
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }

        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            // URL formatı düzeltme
            if (strpos($this->cover_image, 'filament.lumocomm.com/rooms/') !== false) {
                return '/storage/' . str_replace('https://filament.lumocomm.com/', '', $this->cover_image);
            }
            return $this->cover_image;
        }

        // Eğer zaten bir dosya yolu ise /storage/ ile başlatalım
        return '/storage/' . $this->cover_image;
    }

    /**
     * Galeri resimleri URL'leri
     */
    public function getGalleryUrlsAttribute(): array
    {
        $gallery = $this->gallery;
        
        if (empty($gallery)) {
            return [];
        }

        return collect($gallery)->map(function ($image) {
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                // URL formatı düzeltme
                if (strpos($image, 'filament.lumocomm.com/rooms/') !== false) {
                    return '/storage/' . str_replace('https://filament.lumocomm.com/', '', $image);
                }
                return $image;
            }

            // Eğer zaten bir dosya yolu ise /storage/ ile başlatalım
            return '/storage/' . $image;
        })->toArray();
    }

    /**
     * Otel ilişkisi
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Oda tipi ilişkisi
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Oda özellikleri ilişkisi
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(RoomAmenity::class, 'room_room_amenity');
    }
    
    /**
     * Oda pansiyon tipleri ilişkisi
     *
     * NOT: PricingV2 ile yeni mimari kullanıldığında bu kaldırılacak, şimdilik geriye dönük uyumluluk için kalıyor
     */
    // BoardTypes ilişkisi tamamen kaldırıldı - artık otel board_types ilişkisi kullanılacak

    /**
     * Bu odanın fiyatlandırma hesaplama yönteminin açıklamasını döndürür
     *
     * @return string
     */
    public function getPricingCalculationMethodLabelAttribute(): string
    {
        return match($this->pricing_calculation_method) {
            'per_person' => 'Kişi Başı Fiyatlandırma',
            'per_room' => 'Oda Fiyatlandırma (Unit)',
            default => 'Belirtilmemiş'
        };
    }

    /**
     * Slug için mutator - otomatik slug oluşturur
     */
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($room) {
            if (!$room->slug && $room->name) {
                $baseSlug = \Illuminate\Support\Str::slug($room->name);
                $room->slug = $room->hotel_id . '-' . $baseSlug;
            }
        });

        static::updating(function ($room) {
            if ($room->isDirty('name') || $room->isDirty('hotel_id')) {
                $baseSlug = \Illuminate\Support\Str::slug($room->name);
                $room->slug = $room->hotel_id . '-' . $baseSlug;
            }
        });
    }

    /**
     * Rezervasyonlar ilişkisi
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
    
    /**
     * Oda fiyat tarifeleri ilişkisi
     */
    public function rates(): HasMany
    {
        return $this->hasMany(RoomRate::class);
    }
    
    /**
     * Odanın fiyat kuralları
     */
    public function priceRules(): HasMany
    {
        return $this->hasMany(PriceRule::class);
    }
    
    /**
     * Odanın envanter kayıtları
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(RoomInventory::class);
    }

    /**
     * Oluşturan kullanıcı ilişkisi
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Güncelleyen kullanıcı ilişkisi
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}