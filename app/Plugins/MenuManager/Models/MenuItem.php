<?php

namespace App\Plugins\MenuManager\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuItem extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes, HasFactory;
    
    protected $table = 'menu_items';
    
    protected $fillable = [
        'tenant_id', // tenant_id'yi ekledik
        'menu_id',
        'parent_id',
        'title',
        'url',
        'route_name',
        'link_type',
        'target',
        'icon',
        'class',
        'attributes',
        'data',
        'model_type',
        'model_id',
        'order',
        'is_active',
        'is_featured',
        'is_mega_menu',
        'template',
        // Mega menü için ek alanlar
        'mega_menu_layout',
        'mega_menu_content',
        'mega_menu_template',
        'mega_menu_background',
        'mega_menu_columns',
        'mega_menu_width',
        'mega_menu_styles',
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_mega_menu' => 'boolean',
        'attributes' => 'json',
        'data' => 'json',
        'order' => 'integer',
        'mega_menu_layout' => 'json',
        'mega_menu_content' => 'json',
        'mega_menu_styles' => 'json',
        'mega_menu_columns' => 'integer',
    ];
    
    /**
     * Get the menu that this item belongs to.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
    
    /**
     * Get the parent menu item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }
    
    /**
     * Get the child menu items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('order')
            ->with('children'); // Eager load nested children
    }
    
    /**
     * Get the model that this menu item targets, if applicable.
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo('model');
    }
    
    /**
     * Get related template.
     */
    public function templateModel(): BelongsTo
    {
        return $this->belongsTo(MenuItemTemplate::class, 'template', 'slug');
    }
    
    /**
     * Scope active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope featured items.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Scope mega menu items.
     */
    public function scopeMegaMenu($query)
    {
        return $query->where('is_mega_menu', true);
    }
    
    /**
     * Scope root level items (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
    
    /**
     * Determine if the menu item has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }
    
    /**
     * Get the menu item's URL based on its type.
     */
    public function getUrlAttribute($value)
    {
        if ($this->link_type === 'url') {
            return $value;
        }
        
        if ($this->link_type === 'route' && $this->route_name) {
            try {
                return route($this->route_name);
            } catch (\Exception $e) {
                return '#invalid-route';
            }
        }
        
        if ($this->link_type === 'model' && $this->linkable) {
            // Assume the linked model has a url attribute or method
            return $this->linkable->url ?? '#';
        }
        
        return $value ?: '#';
    }
}