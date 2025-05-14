<?php

namespace App\Plugins\ThemeManager\Models;

use App\Plugins\Core\src\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class ThemeSetting extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'key', 
        'value', 
        'type',
        'group',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'value' => 'string',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        // Try to get from cache first
        $cacheKey = 'theme_setting_' . $key;
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // If not in cache, get from database
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        // Cast the value based on the type
        $value = self::castValue($setting->value, $setting->type);
        
        // Cache for future use (5 minutes)
        Cache::put($cacheKey, $value, 300);
        
        return $value;
    }
    
    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $group
     * @param bool $isPublic
     * @return ThemeSetting
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', bool $isPublic = true)
    {
        // Update or create setting
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'is_public' => $isPublic,
            ]
        );
        
        // Update cache
        $cacheKey = 'theme_setting_' . $key;
        $castedValue = self::castValue($value, $type);
        Cache::put($cacheKey, $castedValue, 300);
        
        return $setting;
    }
    
    /**
     * Cast value according to type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
            case 'number':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return (bool) $value;
            case 'json':
                return json_decode($value, true);
            case 'array':
                return json_decode($value, true) ?: [];
            case 'color':
            case 'string':
            default:
                return $value;
        }
    }
    
    /**
     * Get all settings as a key-value array, grouped by their group
     *
     * @param bool $publicOnly
     * @return array
     */
    public static function getAllGrouped(bool $publicOnly = false)
    {
        $query = self::query();
        
        if ($publicOnly) {
            $query->where('is_public', true);
        }
        
        $settings = $query->get();
        
        $result = [];
        
        foreach ($settings as $setting) {
            $value = self::castValue($setting->value, $setting->type);
            
            if (!isset($result[$setting->group])) {
                $result[$setting->group] = [];
            }
            
            $result[$setting->group][$setting->key] = $value;
        }
        
        return $result;
    }
    
    /**
     * Get all settings as a key-value array
     *
     * @param bool $publicOnly
     * @return array
     */
    public static function getAll(bool $publicOnly = false)
    {
        $query = self::query();
        
        if ($publicOnly) {
            $query->where('is_public', true);
        }
        
        $settings = $query->get();
        
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }
        
        return $result;
    }
    
    /**
     * Flush all settings from cache
     */
    public static function flushCache()
    {
        $settings = self::all();
        
        foreach ($settings as $setting) {
            Cache::forget('theme_setting_' . $setting->key);
        }
    }
}