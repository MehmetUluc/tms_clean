<?php

namespace App\Plugins\MenuManager\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use App\Plugins\Core\src\Traits\HasTenant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Burada BaseModel yerine doğrudan Model sınıfını extend ediyoruz
// ve gerekli trait'leri manuel olarak ekliyoruz.
class Menu extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes, HasFactory;
    
    protected $table = 'menus';
    
    protected $fillable = [
        'tenant_id', // tenant_id'yi ekledik
        'name',
        'slug',
        'location',
        'type',
        'is_active',
        'settings',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'json',
    ];
    
    /**
     * Get the menu items associated with this menu.
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('order');
    }
    
    /**
     * Get all menu items (including children) associated with this menu.
     */
    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->orderBy('order');
    }
    
    /**
     * Auto-generate slug from name if not provided.
     */
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value, $attributes) => 
                $value ?? Str::slug($attributes['name'] ?? ''),
        );
    }
    
    /**
     * Scope active menus.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope by location.
     */
    public function scopeLocation($query, $location)
    {
        return $query->where('location', $location);
    }
}