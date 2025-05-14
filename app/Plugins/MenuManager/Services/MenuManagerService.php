<?php

namespace App\Plugins\MenuManager\Services;

use App\Plugins\MenuManager\Models\Menu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class MenuManagerService
{
    /**
     * Get a menu by its slug.
     *
     * @param string $slug
     * @param bool $withItems Include menu items
     * @return Menu|null
     */
    public function getMenu(string $slug, bool $withItems = true): ?Menu
    {
        $cacheEnabled = config('menu-manager.cache.enabled', true);
        $cacheKey = "menu:{$slug}:" . ($withItems ? 'with-items' : 'without-items');
        
        if ($cacheEnabled) {
            return Cache::remember($cacheKey, config('menu-manager.cache.ttl', 1440), function () use ($slug, $withItems) {
                return $this->fetchMenu($slug, $withItems);
            });
        }
        
        return $this->fetchMenu($slug, $withItems);
    }
    
    /**
     * Fetch a menu from the database.
     *
     * @param string $slug
     * @param bool $withItems
     * @return Menu|null
     */
    protected function fetchMenu(string $slug, bool $withItems): ?Menu
    {
        $query = Menu::where('slug', $slug)
            ->where('is_active', true);
        
        if ($withItems) {
            $query->with(['items' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('order')
                    ->with('children');
            }]);
        }
        
        return $query->first();
    }
    
    /**
     * Get a menu by its location.
     *
     * @param string $location
     * @param bool $withItems Include menu items
     * @return Menu|null
     */
    public function getMenuByLocation(string $location, bool $withItems = true): ?Menu
    {
        $cacheEnabled = config('menu-manager.cache.enabled', true);
        $cacheKey = "menu:location:{$location}:" . ($withItems ? 'with-items' : 'without-items');
        
        if ($cacheEnabled) {
            return Cache::remember($cacheKey, config('menu-manager.cache.ttl', 1440), function () use ($location, $withItems) {
                return $this->fetchMenuByLocation($location, $withItems);
            });
        }
        
        return $this->fetchMenuByLocation($location, $withItems);
    }
    
    /**
     * Fetch a menu by location from the database.
     *
     * @param string $location
     * @param bool $withItems
     * @return Menu|null
     */
    protected function fetchMenuByLocation(string $location, bool $withItems): ?Menu
    {
        $query = Menu::where('location', $location)
            ->where('is_active', true);
        
        if ($withItems) {
            $query->with(['items' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('order')
                    ->with('children');
            }]);
        }
        
        return $query->first();
    }
    
    /**
     * Get all available menus.
     *
     * @param bool $activeOnly Only get active menus
     * @return Collection
     */
    public function getAllMenus(bool $activeOnly = true): Collection
    {
        $query = Menu::query();
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->get();
    }
    
    /**
     * Clear menu cache.
     *
     * @param string|null $slug Clear specific menu cache, or all menu cache if null
     * @return void
     */
    public function clearCache(?string $slug = null): void
    {
        if ($slug) {
            Cache::forget("menu:{$slug}:with-items");
            Cache::forget("menu:{$slug}:without-items");
        } else {
            // Clear all menu cache
            $keys = collect(Cache::getStore()->many(Cache::getStore()->all()));
            $menuKeys = $keys->filter(function ($value, $key) {
                return str_starts_with($key, 'menu:');
            })->keys();
            
            foreach ($menuKeys as $key) {
                Cache::forget($key);
            }
        }
    }
    
    /**
     * Reorder menu items.
     *
     * @param array $items Array of item IDs and their order positions
     * @return void
     */
    public function reorderMenuItems(array $items): void
    {
        foreach ($items as $id => $data) {
            $item = \App\Plugins\MenuManager\Models\MenuItem::find($id);
            if ($item) {
                $item->update([
                    'parent_id' => $data['parent_id'] ?? null,
                    'order' => $data['order'] ?? 0,
                ]);
            }
        }
        
        // Clear cache after reordering
        $this->clearCache();
    }
}