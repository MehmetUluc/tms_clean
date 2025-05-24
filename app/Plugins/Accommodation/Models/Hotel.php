<?php

namespace App\Plugins\Accommodation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Accommodation\Scopes\VendorScope;
use App\Models\User;
use App\Plugins\Accommodation\Models\HotelTag;
use App\Plugins\Accommodation\Models\HotelContact;
use App\Plugins\Amenities\Models\HotelAmenity;
use App\Plugins\Pricing\Models\PriceSet;
use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Pricing\Models\RoomInventory;
use App\Plugins\Vendor\Models\Vendor;

class Hotel extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Vendor scope'u uygula
        static::addGlobalScope(new VendorScope);
    }

    /**
     * Kitle atamasına izin verilen özellikler.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'tenant_id',
        'vendor_id',
        'region_id',
        'name',
        'slug',
        'hotel_type_id', // type_id kullanımını düzelt - hotel_type_id olarak değiştirildi
        'star_rating', // stars -> star_rating olarak değiştirildi
        'description',
        'short_description',
        'refund_policy',
        'allow_refundable',
        'allow_non_refundable',
        'non_refundable_discount',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'latitude',
        'longitude',
        'min_price',
        'max_price',
        'currency',
        'amenities',
        'policies',
        'check_in_out',
        'child_policies',
        'max_children_per_room',
        'child_age_limit',
        'children_stay_free',
        'child_policy_description',
        'cover_image',
        'gallery',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
        'is_featured',
    ];

    /**
     * JSON verileri dönüştürme.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'star_rating' => 'integer', // stars -> star_rating olarak değiştirildi
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'allow_refundable' => 'boolean',
        'allow_non_refundable' => 'boolean',
        'non_refundable_discount' => 'decimal:2',
    ];

    /**
     * non_refundable_discount değerini ayarla
     * null değer gelirse 0 olarak ayarla
     */
    public function setNonRefundableDiscountAttribute($value)
    {
        $this->attributes['non_refundable_discount'] = $value === null ? 0 : $value;
    }
    
    /**
     * Model oluşturulduğunda varsayılan değerler.
     *
     * @var array
     */
    protected $attributes = [
        'amenities' => '[]',
        'policies' => '[]',
        'gallery' => '[]',
        'check_in_out' => '{"check_in_from":"14:00","check_in_until":"23:59","check_out_from":"07:00","check_out_until":"12:00"}',
        'child_policies' => '[]',
        'max_children_per_room' => 2,
        'child_age_limit' => 12,
        'children_stay_free' => false,
    ];

    /**
     * JSON verilerin işlenmesi için accessor'lar
     */
    public function getAmenitiesAttribute($value)
    {
        return json_decode($value ?: '[]', true);
    }
    
    public function getPoliciesAttribute($value)
    {
        return json_decode($value ?: '[]', true);
    }
    
    public function getGalleryAttribute($value)
    {
        return json_decode($value ?: '[]', true);
    }
    
    public function getCheckInOutAttribute($value)
    {
        $default = [
            'check_in_from' => '14:00',
            'check_in_until' => '23:59',
            'check_out_from' => '07:00',
            'check_out_until' => '12:00'
        ];
        
        $decoded = json_decode($value, true);
        return $decoded ?: $default;
    }
    
    /**
     * JSON verilerin işlenmesi için mutator'lar
     */
    public function setAmenitiesAttribute($value)
    {
        $this->attributes['amenities'] = is_string($value) ? $value : json_encode($value ?: []);
    }
    
    public function setPoliciesAttribute($value)
    {
        $this->attributes['policies'] = is_string($value) ? $value : json_encode($value ?: []);
    }
    
    public function setGalleryAttribute($value)
    {
        $this->attributes['gallery'] = is_string($value) ? $value : json_encode($value ?: []);
    }
    
    public function setCheckInOutAttribute($value)
    {
        $default = [
            'check_in_from' => '14:00',
            'check_in_until' => '23:59',
            'check_out_from' => '07:00',
            'check_out_until' => '12:00'
        ];
        
        if (is_string($value)) {
            $this->attributes['check_in_out'] = $value;
        } else {
            $this->attributes['check_in_out'] = json_encode($value ?: $default);
        }
    }
    
    /**
     * Get minimum price from daily rates
     */
    public function getMinPriceAttribute()
    {
        // Get minimum price from daily rates through rate plans
        $minPrice = $this->rooms()
            ->join('rate_plans', 'rooms.id', '=', 'rate_plans.room_id')
            ->join('daily_rates', 'rate_plans.id', '=', 'daily_rates.rate_plan_id')
            ->where('daily_rates.date', '>=', now()->toDateString())
            ->where('daily_rates.is_closed', false)
            ->min('daily_rates.base_price');
            
        return $minPrice ?? 500; // Default to 500 if no rates found
    }
    
    /**
     * Child policies accessor
     */
    public function getChildPoliciesAttribute($value)
    {
        return json_decode($value ?: '[]', true);
    }
    
    /**
     * Child policies mutator
     */
    public function setChildPoliciesAttribute($value)
    {
        $this->attributes['child_policies'] = is_string($value) ? $value : json_encode($value ?: []);
    }

    /**
     * Oluşturan kullanıcı ilişkisi.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Güncelleyen kullanıcı ilişkisi.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Kapak resmi URL'si özelliği.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }

        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            // URL formatı düzeltme
            if (strpos($this->cover_image, 'filament.lumocomm.com/hotels/') !== false) {
                return '/storage/' . str_replace('https://filament.lumocomm.com/', '', $this->cover_image);
            }
            return $this->cover_image;
        }

        // Eğer zaten bir dosya yolu ise /storage/ ile başlatalım
        return '/storage/' . $this->cover_image;
    }

    /**
     * Galeri resimleri URL'leri özelliği.
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
                if (strpos($image, 'filament.lumocomm.com/hotels/') !== false) {
                    return '/storage/' . str_replace('https://filament.lumocomm.com/', '', $image);
                }
                return $image;
            }

            // Eğer zaten bir dosya yolu ise /storage/ ile başlatalım
            return '/storage/' . $image;
        })->toArray();
    }

    /**
     * Yıldız sayısı formatlanmış olarak.
     */
    public function getStarsFormattedAttribute(): string
    {
        return str_repeat('★', $this->star_rating);
    }

    /**
     * Otelin tam adresini döndürür.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Bölge ilişkisi.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
    
    /**
     * Otel tipi ilişkisi.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(HotelType::class, 'hotel_type_id');
    }

    /**
     * Etiketler ilişkisi.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(HotelTag::class, 'hotel_hotel_tag');
    }
    
    /**
     * Otel özellikleri ilişkisi.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(HotelAmenity::class, 'hotel_hotel_amenity');
    }

    /**
     * Otel iletişim kişileri ilişkisi.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(HotelContact::class);
    }
    
    /**
     * Odalar ilişkisi.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
    
    /**
     * Rezervasyonlar ilişkisi.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
    
    /**
     * Otelin fiyat setleri ilişkisi.
     */
    public function priceSets(): HasMany
    {
        return $this->hasMany(PriceSet::class);
    }
    
    /**
     * Otelin envanter kayıtları.
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(RoomInventory::class);
    }

    /**
     * Otelin pansiyon tipleri (Hotel Board Types) ilişkisi - PricingV2 Plugin
     */
    public function hotelBoardTypes(): HasMany
    {
        return $this->hasMany(\App\Plugins\PricingV2\Models\HotelBoardType::class);
    }

    /**
     * Otelin rate planları ilişkisi - PricingV2 Plugin
     */
    public function ratePlans(): HasMany
    {
        return $this->hasMany(\App\Plugins\PricingV2\Models\RatePlanV2::class);
    }

    /**
     * Otelin board tipleri (Doğrudan ilişki)
     * Pivot içinde pricing_calculation_method alanı var
     */
    public function boardTypes(): BelongsToMany
    {
        return $this->belongsToMany(\App\Plugins\Booking\Models\BoardType::class, 'hotel_board_types')
                    ->withPivot(['pricing_calculation_method'])
                    ->withTimestamps();
    }

    /**
     * Vendor (Partner) ilişkisi.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}