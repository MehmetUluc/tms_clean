<?php

namespace App\Plugins\OTA\Services;

use App\Plugins\OTA\Models\DataMapping;
use App\Plugins\OTA\Models\Channel;
use App\Plugins\OTA\Services\Parsers\XmlDataParser;
use App\Plugins\OTA\Services\Parsers\JsonDataParser;
use App\Plugins\OTA\Contracts\IDataParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataMappingService
{
    /**
     * Available data parsers
     *
     * @var array
     */
    protected array $parsers = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // Register available parsers
        $this->registerParser(new XmlDataParser());
        $this->registerParser(new JsonDataParser());
    }
    
    /**
     * Register a data parser
     *
     * @param IDataParser $parser
     * @return void
     */
    public function registerParser(IDataParser $parser): void
    {
        $this->parsers[$parser->getFormatType()] = $parser;
    }
    
    /**
     * Get parser by format type
     *
     * @param string $formatType
     * @return IDataParser|null
     */
    public function getParser(string $formatType): ?IDataParser
    {
        return $this->parsers[$formatType] ?? null;
    }
    
    /**
     * Detect the format of content and return appropriate parser
     *
     * @param string $content
     * @return IDataParser|null
     */
    public function detectFormat(string $content): ?IDataParser
    {
        foreach ($this->parsers as $parser) {
            if ($parser->canParse($content)) {
                return $parser;
            }
        }
        
        return null;
    }
    
    /**
     * Get available system fields based on entity type
     *
     * @param string $entityType
     * @return array
     */
    public function getSystemFields(string $entityType): array
    {
        $fields = [];
        
        // Determine table name based on entity type
        $tableName = $this->getTableNameFromEntity($entityType);
        
        if ($tableName && Schema::hasTable($tableName)) {
            // Get all columns from the table
            $columns = Schema::getColumnListing($tableName);
            
            foreach ($columns as $column) {
                $type = Schema::getColumnType($tableName, $column);
                
                $fields[$column] = [
                    'name' => $column,
                    'type' => $type,
                    'description' => $this->getFieldDescription($tableName, $column)
                ];
            }
            
            // Add relations
            $relationFields = $this->getRelationFields($entityType);
            $fields = array_merge($fields, $relationFields);
        }
        
        // Sort system fields alphabetically
        ksort($fields);
        
        return $fields;
    }
    
    /**
     * Get table name based on entity type
     *
     * @param string $entityType
     * @return string|null
     */
    protected function getTableNameFromEntity(string $entityType): ?string
    {
        $mapping = [
            'room' => 'rooms',
            'rate' => 'rate_plans',
            'availability' => 'inventories',
            'reservation' => 'reservations',
            'hotel' => 'hotels',
            'guest' => 'guests',
        ];
        
        return $mapping[$entityType] ?? null;
    }
    
    /**
     * Get field description for better UI experience
     *
     * @param string $tableName
     * @param string $columnName
     * @return string
     */
    protected function getFieldDescription(string $tableName, string $columnName): string
    {
        // Field descriptions - can be extended in the future
        $descriptions = [
            'rooms.id' => 'Room ID',
            'rooms.hotel_id' => 'Hotel ID',
            'rooms.room_type_id' => 'Room type ID',
            'rooms.name' => 'Room name',
            'rooms.code' => 'Room code',
            'rooms.description' => 'Room description',
            'rooms.capacity' => 'Maximum capacity',
            'rooms.base_occupancy' => 'Base occupancy',
            'rooms.extra_bed' => 'Extra bed available',
            'rooms.is_active' => 'Room is active',
            
            'rate_plans.id' => 'Rate plan ID',
            'rate_plans.room_id' => 'Room ID',
            'rate_plans.name' => 'Rate plan name',
            'rate_plans.code' => 'Rate plan code',
            'rate_plans.is_active' => 'Rate plan is active',
            
            'inventories.id' => 'Inventory ID',
            'inventories.room_id' => 'Room ID',
            'inventories.date' => 'Date',
            'inventories.available' => 'Available rooms',
            
            'reservations.id' => 'Reservation ID',
            'reservations.room_id' => 'Room ID',
            'reservations.guest_id' => 'Guest ID',
            'reservations.checkin_date' => 'Check-in date',
            'reservations.checkout_date' => 'Check-out date',
            'reservations.adults' => 'Number of adults',
            'reservations.children' => 'Number of children',
            'reservations.rate_amount' => 'Total rate',
            'reservations.status' => 'Status',
            
            'hotels.id' => 'Hotel ID',
            'hotels.name' => 'Hotel name',
            'hotels.region_id' => 'Region ID',
            'hotels.description' => 'Hotel description',
            'hotels.address' => 'Address',
            'hotels.is_active' => 'Hotel is active',
            
            'guests.id' => 'Guest ID',
            'guests.first_name' => 'First name',
            'guests.last_name' => 'Last name',
            'guests.email' => 'Email',
            'guests.phone' => 'Phone'
        ];
        
        $key = "$tableName.$columnName";
        return $descriptions[$key] ?? $columnName;
    }
    
    /**
     * Get relation fields for the entity
     *
     * @param string $entityType
     * @return array
     */
    protected function getRelationFields(string $entityType): array
    {
        $relations = [];
        
        // Entity-specific relation fields
        switch ($entityType) {
            case 'room':
                $relations['hotel.name'] = [
                    'name' => 'hotel.name',
                    'type' => 'relation',
                    'description' => 'Hotel name'
                ];
                $relations['room_type.name'] = [
                    'name' => 'room_type.name',
                    'type' => 'relation',
                    'description' => 'Room type name'
                ];
                break;
                
            case 'reservation':
                $relations['guest.full_name'] = [
                    'name' => 'guest.full_name',
                    'type' => 'relation',
                    'description' => 'Guest full name'
                ];
                $relations['room.name'] = [
                    'name' => 'room.name',
                    'type' => 'relation',
                    'description' => 'Room name'
                ];
                $relations['hotel.name'] = [
                    'name' => 'hotel.name',
                    'type' => 'relation',
                    'description' => 'Hotel name'
                ];
                break;
                
            // Other entity types and relations can be added here
        }
        
        return $relations;
    }
    
    /**
     * Save mapping configuration
     *
     * @param Channel $channel
     * @param string $entityType
     * @param string $name
     * @param array $mappingData
     * @param string $operationType
     * @param string $formatType
     * @param string|null $description
     * @param string|null $templateContent
     * @param string|null $templateFormat
     * @return DataMapping
     */
    public function saveMapping(
        Channel $channel,
        string $entityType,
        string $name,
        array $mappingData,
        string $operationType = 'import',
        string $formatType = 'xml',
        ?string $description = null,
        ?string $templateContent = null,
        ?string $templateFormat = null
    ): DataMapping {
        return DataMapping::create([
            'channel_id' => $channel->id,
            'name' => $name,
            'operation_type' => $operationType,
            'format_type' => $formatType,
            'mapping_entity' => $entityType,
            'mapping_data' => $mappingData,
            'description' => $description,
            'template_content' => $templateContent,
            'template_format' => $templateFormat,
            'is_active' => true,
        ]);
    }
    
    /**
     * Get transformation options
     *
     * @return array
     */
    public function getTransformationOptions(): array
    {
        return [
            'date_format' => [
                'dd-mm-yyyy => yyyy-mm-dd',
                'dd/mm/yyyy => yyyy-mm-dd',
                'mm-dd-yyyy => yyyy-mm-dd',
                'mm/dd/yyyy => yyyy-mm-dd',
                'yyyy-mm-dd => yyyy-mm-dd',
            ],
            'currency' => [
                'USD => TRY',
                'EUR => TRY',
                'GBP => TRY',
            ],
            'boolean' => [
                'Y/N => 1/0',
                'Yes/No => 1/0',
                'True/False => 1/0',
                '1/0 => True/False',
            ]
        ];
    }
    
    /**
     * Get available currencies
     *
     * @return array
     */
    public function getAvailableCurrencies(): array
    {
        return [
            'TRY' => 'Turkish Lira',
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
        ];
    }
    
    /**
     * Get available format types
     *
     * @return array
     */
    public function getAvailableFormats(): array
    {
        return [
            'xml' => 'XML',
            'json' => 'JSON',
        ];
    }
    
    /**
     * Get available operation types
     *
     * @return array
     */
    public function getAvailableOperations(): array
    {
        return [
            'import' => 'Import (from external to system)',
            'export' => 'Export (from system to external)',
        ];
    }
    
    /**
     * Process data through a mapping
     *
     * @param string $content
     * @param DataMapping $mapping
     * @return mixed
     */
    public function processData(string $content, DataMapping $mapping): mixed
    {
        $parser = $this->getParser($mapping->format_type);
        
        if (!$parser) {
            return ['error' => 'No parser available for format: ' . $mapping->format_type];
        }
        
        return $parser->transform(
            $content, 
            $mapping->mapping_data, 
            $mapping->isExport() ? $mapping->template_content : null
        );
    }
}