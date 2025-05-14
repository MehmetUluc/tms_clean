<?php

namespace App\Plugins\Accommodation\Models;

use App\Plugins\Accommodation\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Schema;
use App\Plugins\Core\src\Models\BaseModel;

class Region extends BaseModel
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
        'parent_id', // Üst bölge referansı
        'name',
        'type', // country, region, city, district
        'slug',
        'code', // Ülke kodu, bölge kodu
        'description',
        'latitude',
        'longitude', 
        'timezone',
        'sort_order',
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
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'sort_order' => 'integer',
    ];
    
    /**
     * Bölge tiplerinin tanımlanması
     */
    const TYPE_COUNTRY = 'country';   // Ülke
    const TYPE_REGION = 'region';     // Bölge (Ege, Akdeniz, Karadeniz, vb)
    const TYPE_CITY = 'city';         // Şehir (İzmir, Antalya, Girne, vb)
    const TYPE_DISTRICT = 'district'; // İlçe/Semt (Çiğli, Konyaaltı, vb)
    
    /**
     * Bölge tiplerinin Türkçe karşılıkları
     */
    public static function getTypeLabels(): array
    {
        return [
            self::TYPE_COUNTRY => 'Ülke',
            self::TYPE_REGION => 'Bölge',
            self::TYPE_CITY => 'Şehir',
            self::TYPE_DISTRICT => 'İlçe/Semt',
        ];
    }
    
    /**
     * Türkçe bölge tipini döndür
     */
    public function getTypeLabel(): string
    {
        if ($this->type === null) {
            return 'Belirtilmemiş';
        }
        
        $labels = self::getTypeLabels();
        return $labels[$this->type] ?? (string)$this->type;
    }

    /**
     * Üst bölge ilişkisi (parent)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    /**
     * Alt bölgeler ilişkisi (children)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Region::class, 'parent_id');
    }
    
    /**
     * Aktif alt bölgeler
     */
    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true)->orderBy('sort_order');
    }
    
    /**
     * Tüm alt bölgeler (ve onların alt bölgeleri) - Recursive
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }
    
    /**
     * Tüm üst bölgeler (ve onların üst bölgeleri) - Recursive
     */
    public function allParents()
    {
        return $this->parent()->with('allParents');
    }

    /**
     * Bu bölgedeki oteller ilişkisi.
     */
    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }
    
    /**
     * Tüm alt bölgeler dahil oteller
     */
    public function allHotels(): HasManyThrough
    {
        return $this->hasManyThrough(
            Hotel::class,
            Region::class,
            'parent_id', // Region'daki foreign key
            'region_id', // Hotel'deki foreign key
            'id', // Region'daki local key
            'id'  // Region'daki local key
        );
    }
    
    /**
     * Bölgenin tüm alt bölgeleri dahil tüm otellerini almak için.
     */
    public function getAllHotelsAttribute(): Collection
    {
        // Kendi otelleri
        $hotels = $this->hotels()->get();
        
        // Alt bölgelerdeki tüm otelleri al
        $childRegionIds = $this->getAllChildrenIdsAttribute();
        if (!empty($childRegionIds)) {
            $childHotels = Hotel::whereIn('region_id', $childRegionIds)->get();
            $hotels = $hotels->merge($childHotels);
        }
        
        return $hotels;
    }
    
    /**
     * Bölgenin tüm alt bölge ID'lerini almak için.
     */
    public function getAllChildrenIdsAttribute(): array
    {
        $allChildren = collect();
        $this->getChildrenRecursive($this, $allChildren);
        return $allChildren->pluck('id')->toArray();
    }
    
    /**
     * Recursive olarak tüm alt bölgeleri toplamak için yardımcı metod.
     */
    private function getChildrenRecursive(Region $region, \Illuminate\Support\Collection &$collection): void
    {
        $children = $region->children()->get();
        
        foreach ($children as $child) {
            $collection->push($child);
            $this->getChildrenRecursive($child, $collection);
        }
    }
    
    /**
     * Ülke tipindeki (TYPE_COUNTRY) bölgeleri döndürür
     */
    public function scopeCountries(Builder $query): Builder
    {
        if (Schema::hasColumn('regions', 'type')) {
            return $query->where('type', self::TYPE_COUNTRY);
        }
        return $query;
    }
    
    /**
     * Ana bölge tipindeki (TYPE_REGION) bölgeleri döndürür
     */
    public function scopeMainRegions(Builder $query): Builder
    {
        if (Schema::hasColumn('regions', 'type')) {
            return $query->where('type', self::TYPE_REGION);
        }
        return $query;
    }
    
    /**
     * Şehir tipindeki (TYPE_CITY) bölgeleri döndürür
     */
    public function scopeCities(Builder $query): Builder
    {
        if (Schema::hasColumn('regions', 'type')) {
            return $query->where('type', self::TYPE_CITY);
        }
        return $query;
    }
    
    /**
     * İlçe tipindeki (TYPE_DISTRICT) bölgeleri döndürür
     */
    public function scopeDistricts(Builder $query): Builder
    {
        if (Schema::hasColumn('regions', 'type')) {
            return $query->where('type', self::TYPE_DISTRICT);
        }
        return $query;
    }
    
    /**
     * Öne çıkan bölgeleri döndürür
     */
    public function scopeFeatured(Builder $query): Builder
    {
        if (Schema::hasColumn('regions', 'is_featured')) {
            return $query->where('is_featured', true);
        }
        return $query;
    }
    
    /**
     * Üst bölgesi olmayan (ana) bölgeleri döndürür
     */
    public function scopeRoot(Builder $query): Builder
    {
        if (Schema::hasColumn('regions', 'parent_id')) {
            return $query->whereNull('parent_id');
        }
        return $query;
    }
    
    /**
     * Belirli bir bölgenin alt bölgelerini döndürür
     */
    public function scopeChildrenOf(Builder $query, int $parentId): Builder
    {
        if (Schema::hasColumn('regions', 'parent_id')) {
            return $query->where('parent_id', $parentId);
        }
        return $query;
    }
    
    /**
     * Bölge tipine göre meta başlık oluşturur
     */
    public function getMetaTitle(): string
    {
        $name = $this->name ?? 'Belirtilmemiş';
        
        if (!Schema::hasColumn('regions', 'type')) {
            return "{$name} Otelleri";
        }
        
        $typeLabel = $this->getTypeLabel();
        
        switch ($this->type) {
            case self::TYPE_COUNTRY:
                return "{$name} Otelleri ve Konaklama";
            case self::TYPE_REGION:
                return "{$name} Bölgesi Otelleri ve Konaklama";
            case self::TYPE_CITY:
                return "{$name} Otelleri, En Uygun {$name} Otel Fiyatları";
            case self::TYPE_DISTRICT:
                $parentName = Schema::hasColumn('regions', 'parent_id') ? ($this->parent?->name ?? '') : '';
                return "{$name} {$parentName} Otelleri, En Uygun Fiyatlar";
            default:
                return "{$name} Otelleri";
        }
    }
    
    /**
     * Tam konum bilgisini formatlanmış olarak döndürür
     * Örnek: "Çiğli, İzmir, Ege Bölgesi, Türkiye"
     */
    public function getFullLocation(): string
    {
        $location = [$this->name ?? 'Belirtilmemiş'];
        
        // Schema::hasColumn kontrol et ve üst bölgeleri ekle
        if (Schema::hasColumn('regions', 'parent_id')) {
            $parent = $this->parent;
            while ($parent) {
                $location[] = $parent->name ?? 'Belirtilmemiş';
                $parent = $parent->parent;
            }
        }
        
        return implode(', ', $location);
    }
    
    /**
     * Üst bölgelerin tam yolunu oluşturan özellik.
     * Örn: "Türkiye > Ege Bölgesi > İzmir > Çeşme"
     */
    public function getFullPathAttribute(): string
    {
        if ($this->name === null) {
            return 'Belirtilmemiş';
        }
        
        // parent_id sütunu yoksa, sadece adı döndür
        if (!Schema::hasColumn('regions', 'parent_id')) {
            return $this->name;
        }
    
        $pathParts = collect([$this->name]);
        $parent = $this->parent;
        
        while ($parent) {
            if ($parent->name !== null) {
                $pathParts->prepend($parent->name);
            } else {
                $pathParts->prepend('Belirtilmemiş');
            }
            $parent = $parent->parent;
        }
        
        return $pathParts->implode(' > ');
    }
    
    /**
     * Breadcrumb için bölge hiyerarşisini döndürür
     * En üstten (ülke) en alta (mevcut bölge) doğru sıralanır
     */
    public function getBreadcrumbHierarchy(): array
    {
        $hierarchy = [$this];
        
        // parent_id sütunu yoksa, sadece bu bölgeyi döndür
        if (!Schema::hasColumn('regions', 'parent_id')) {
            return $hierarchy;
        }
        
        // Üst bölgeleri ekle
        $parent = $this->parent;
        while ($parent) {
            array_unshift($hierarchy, $parent);
            $parent = $parent->parent;
        }
        
        return $hierarchy;
    }
}