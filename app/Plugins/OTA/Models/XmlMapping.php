<?php

namespace App\Plugins\OTA\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Plugins\Core\src\Models\BaseModel;

class XmlMapping extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'channel_id',
        'name',
        'slug',
        'direction', // 'import' or 'export'
        'format_type', // 'xml' or 'json'
        'entity_type', // 'room', 'rate', 'availability', etc.
        'description',
        'xml_root_path',
        'field_mappings',
        'value_transformations',
        'sample_data',
        'template_content',
        'template_format',
        'is_active',
        'tenant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'field_mappings' => 'json',
        'value_transformations' => 'json',
        'sample_data' => 'json',
    ];

    /**
     * The attributes that have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'format_type' => 'xml',
    ];

    /**
     * Get the channel that owns the mapping.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Check if this is an import mapping
     *
     * @return bool
     */
    public function isImport(): bool
    {
        return $this->direction === 'import';
    }

    /**
     * Check if this is an export mapping
     *
     * @return bool
     */
    public function isExport(): bool
    {
        return $this->direction === 'export';
    }

    /**
     * Check if this is an XML format mapping
     *
     * @return bool
     */
    public function isXml(): bool
    {
        return $this->format_type === 'xml';
    }

    /**
     * Check if this is a JSON format mapping
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->format_type === 'json';
    }
}