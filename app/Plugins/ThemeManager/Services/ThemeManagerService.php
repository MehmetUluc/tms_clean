<?php

namespace App\Plugins\ThemeManager\Services;

use App\Plugins\ThemeManager\Models\ThemeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class ThemeManagerService
{
    /**
     * Get theme setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return ThemeSetting::get($key, $default);
    }
    
    /**
     * Set theme setting
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param bool $isPublic
     * @return \App\Plugins\ThemeManager\Models\ThemeSetting
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general', bool $isPublic = true)
    {
        return ThemeSetting::set($key, $value, $type, $group, $isPublic);
    }
    
    /**
     * Get all theme settings
     *
     * @param bool $publicOnly
     * @return array
     */
    public function all(bool $publicOnly = false)
    {
        return ThemeSetting::getAll($publicOnly);
    }
    
    /**
     * Get grouped theme settings
     *
     * @param bool $publicOnly
     * @return array
     */
    public function allGrouped(bool $publicOnly = false)
    {
        return ThemeSetting::getAllGrouped($publicOnly);
    }
    
    /**
     * Get settings for a specific group
     *
     * @param string $group
     * @param bool $publicOnly
     * @return array
     */
    public function getGroup(string $group, bool $publicOnly = false)
    {
        $allSettings = $this->allGrouped($publicOnly);
        return $allSettings[$group] ?? [];
    }
    
    /**
     * Get color settings
     *
     * @return array
     */
    public function getColors()
    {
        return $this->getGroup('colors');
    }
    
    /**
     * Get SEO settings
     *
     * @return array
     */
    public function getSeo()
    {
        return $this->getGroup('seo');
    }
    
    /**
     * Get social media settings
     *
     * @return array
     */
    public function getSocial()
    {
        return $this->getGroup('social');
    }
    
    /**
     * Get logo settings
     *
     * @return array
     */
    public function getLogos()
    {
        return $this->getGroup('logos');
    }
    
    /**
     * Get contact information
     *
     * @return array
     */
    public function getContact()
    {
        return $this->getGroup('contact');
    }
    
    /**
     * Get layout settings
     *
     * @return array
     */
    public function getLayout()
    {
        return $this->getGroup('layout');
    }
    
    /**
     * Get typography settings
     *
     * @return array
     */
    public function getTypography()
    {
        return $this->getGroup('typography');
    }
    
    /**
     * Clear all theme settings cache
     */
    public function clearCache()
    {
        ThemeSetting::flushCache();
    }
    
    /**
     * Import theme settings from array
     *
     * @param array $settings
     * @return void
     */
    public function import(array $settings)
    {
        foreach ($settings as $key => $setting) {
            if (is_array($setting) && isset($setting['value'])) {
                $this->set(
                    $key, 
                    $setting['value'],
                    $setting['type'] ?? 'string',
                    $setting['group'] ?? 'general',
                    $setting['is_public'] ?? true
                );
            } else {
                $this->set($key, $setting);
            }
        }
    }
    
    /**
     * Export all theme settings
     *
     * @param bool $publicOnly
     * @return array
     */
    public function export(bool $publicOnly = false)
    {
        $settings = ThemeSetting::when($publicOnly, function($query) {
                return $query->where('is_public', true);
            })
            ->get(['key', 'value', 'type', 'group', 'is_public'])
            ->toArray();
            
        return $settings;
    }
    
    /**
     * Check if a theme setting exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return ThemeSetting::where('key', $key)->exists();
    }
    
    /**
     * Delete a theme setting
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $deleted = ThemeSetting::where('key', $key)->delete();
        
        if ($deleted) {
            Cache::forget('theme_setting_' . $key);
        }
        
        return (bool) $deleted;
    }
    
    /**
     * Initialize default theme settings
     *
     * @return void
     */
    public function initializeDefaults()
    {
        $defaults = config('theme-manager.defaults', []);
        
        foreach ($defaults as $key => $setting) {
            if (!$this->has($key)) {
                if (is_array($setting) && isset($setting['value'])) {
                    $this->set(
                        $key, 
                        $setting['value'],
                        $setting['type'] ?? 'string',
                        $setting['group'] ?? 'general',
                        $setting['is_public'] ?? true
                    );
                } else {
                    $group = 'general';
                    $type = 'string';
                    
                    // Try to determine group and type automatically
                    if (str_starts_with($key, 'color_') || str_contains($key, '_color')) {
                        $group = 'colors';
                        $type = 'color';
                    } elseif (str_starts_with($key, 'seo_') || str_contains($key, '_seo')) {
                        $group = 'seo';
                    } elseif (str_starts_with($key, 'social_') || str_contains($key, '_social')) {
                        $group = 'social';
                    } elseif (str_starts_with($key, 'layout_') || str_contains($key, '_layout')) {
                        $group = 'layout';
                    } elseif (str_starts_with($key, 'typography_') || str_contains($key, '_typography')) {
                        $group = 'typography';
                    } elseif (str_contains($key, 'logo') || str_contains($key, 'image')) {
                        $group = 'logos';
                        $type = 'image';
                    } elseif (str_contains($key, 'contact') || str_contains($key, 'phone') || str_contains($key, 'email') || str_contains($key, 'address')) {
                        $group = 'contact';
                    }
                    
                    $this->set($key, $setting, $type, $group);
                }
            }
        }
    }
}