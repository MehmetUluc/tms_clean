<?php

namespace App\Plugins\OTA\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Plugins\Core\src\Models\BaseModel;

class DataMapping extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_mappings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'channel_id',
        'name',
        'operation_type', // 'import' or 'export'
        'format_type',    // 'xml', 'json', etc.
        'mapping_data',
        'description',
        'is_active',
        'last_sync_at',
        'mapping_entity', // 'room', 'rate', 'availability', etc.
        'validation_rules',
        'template_content', // For storing templates
        'template_format',  // Output format
        'version',          // For template versioning
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'mapping_data' => 'json',
        'validation_rules' => 'json',
        'last_sync_at' => 'datetime',
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
        return $this->operation_type === 'import';
    }

    /**
     * Check if this is an export mapping
     *
     * @return bool
     */
    public function isExport(): bool
    {
        return $this->operation_type === 'export';
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

    /**
     * Get the path separator based on format type
     *
     * @return string
     */
    public function getPathSeparator(): string
    {
        return $this->isXml() ? '.' : '.';
    }
}