<?php

namespace App\Plugins\MenuManager\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItemTemplate extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes, HasFactory;
    
    protected $table = 'menu_item_templates';
    
    protected $fillable = [
        'tenant_id', // tenant_id'yi ekledik
        'name',
        'slug',
        'description',
        'template',
        'settings',
        'fields',
        'thumbnail',
        'is_active',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'json',
        'fields' => 'json',
    ];
    
    /**
     * Get the menu items that use this template.
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'template', 'slug');
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
     * Scope active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}