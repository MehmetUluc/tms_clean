<?php

namespace App\Plugins\OTA\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\OTA\Models\DataMapping;
use App\Plugins\OTA\Models\XmlMapping;

class Channel extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'logo',
        'settings',
        'credentials',
        'is_active',
        'is_enabled',
        'last_sync_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_enabled' => 'boolean',
        'settings' => 'json',
        'credentials' => 'json',
        'last_sync_at' => 'datetime',
    ];

    /**
     * Get the mappings for the channel.
     */
    public function mappings(): HasMany
    {
        return $this->hasMany(DataMapping::class);
    }

    /**
     * Legacy mappings relationship
     */
    public function xmlMappings(): HasMany
    {
        return $this->hasMany(XmlMapping::class);
    }

    /**
     * Get the channel's full import URL.
     *
     * @return string
     */
    public function getImportUrlAttribute(): string
    {
        if (empty($this->settings['import_endpoint'])) {
            return '';
        }

        return $this->settings['import_endpoint'];
    }

    /**
     * Get the channel's full export URL.
     *
     * @return string
     */
    public function getExportUrlAttribute(): string
    {
        if (empty($this->settings['export_endpoint'])) {
            return '';
        }

        return $this->settings['export_endpoint'];
    }
    
    /**
     * Get API Key from credentials
     */
    public function getApiKeyAttribute(): ?string
    {
        return $this->credentials['api_key'] ?? null;
    }
    
    /**
     * Get API Secret from credentials
     */
    public function getApiSecretAttribute(): ?string
    {
        return $this->credentials['api_secret'] ?? null;
    }
}