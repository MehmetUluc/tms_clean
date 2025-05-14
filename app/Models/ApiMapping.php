<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiMapping extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'api_user_id',
        'name',
        'source_type',
        'endpoint_path',
        'target_model',
        'target_operation',
        'frequency',
        'field_mappings',
        'test_data',
        'pre_processor_class',
        'post_processor_class',
        'description',
        'is_active',
        'last_sync_at',
        'validation_rules',
        'schema',
        'last_error',
    ];

    protected $casts = [
        'field_mappings' => 'array',
        'test_data' => 'array',
        'validation_rules' => 'array',
        'schema' => 'array',
        'last_error' => 'array',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    public function apiUser(): BelongsTo
    {
        return $this->belongsTo(ApiUser::class);
    }

    public function getTargetModelInstance()
    {
        $modelClass = $this->target_model;
        return new $modelClass;
    }

    public function generateEndpointPath(): string
    {
        $slug = \Illuminate\Support\Str::slug($this->name);
        $uniqueId = \Illuminate\Support\Str::random(8);
        return "/api/v1/{$slug}-{$uniqueId}";
    }

    /**
     * Process incoming data according to the mapping rules
     * 
     * @param array|string $data Raw data from API request (JSON, XML, etc.)
     * @return array Processed data
     */
    public function processIncomingData($data)
    {
        // This is a placeholder for the actual implementation
        // In a real application, this would parse the incoming data format (XML/JSON)
        // and map it according to field_mappings
        
        $parsedData = $this->parseIncomingData($data);
        
        // Apply pre-processor if defined
        if ($this->pre_processor_class && class_exists($this->pre_processor_class)) {
            $processor = new $this->pre_processor_class();
            $parsedData = $processor->process($parsedData, $this);
        }
        
        // Map fields according to field_mappings
        $mappedData = $this->mapFields($parsedData);
        
        // Apply post-processor if defined
        if ($this->post_processor_class && class_exists($this->post_processor_class)) {
            $processor = new $this->post_processor_class();
            $mappedData = $processor->process($mappedData, $this);
        }
        
        return $mappedData;
    }
    
    /**
     * Parse incoming data based on source_type
     */
    protected function parseIncomingData($data)
    {
        if (is_array($data)) {
            return $data; // Already parsed
        }
        
        switch ($this->source_type) {
            case 'json':
                return json_decode($data, true);
            case 'xml':
                // Simple XML to array conversion
                $xml = simplexml_load_string($data);
                return json_decode(json_encode($xml), true);
            // Add more formats as needed
            default:
                return $data;
        }
    }
    
    /**
     * Map fields according to field_mappings
     */
    protected function mapFields(array $data): array
    {
        $result = [];
        
        foreach ($this->field_mappings as $sourceField => $targetField) {
            // Support for dot notation in source fields (e.g. "address.city")
            $value = data_get($data, $sourceField);
            
            // Support for complex mapping with transformations
            if (is_array($targetField) && isset($targetField['field'])) {
                $targetFieldName = $targetField['field'];
                
                // Apply transformation if defined
                if (isset($targetField['transform']) && is_callable($targetField['transform'])) {
                    $value = $targetField['transform']($value, $data);
                }
            } else {
                $targetFieldName = $targetField;
            }
            
            data_set($result, $targetFieldName, $value);
        }
        
        return $result;
    }
}
