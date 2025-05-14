<?php

namespace App\Plugins\OTA\Services;

use App\Plugins\OTA\Models\XmlMapping;
use App\Plugins\OTA\Models\Channel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MappingService
{
    /**
     * Get available system fields based on entity type
     *
     * @param string $entityType
     * @return array
     */
    public function getSystemFields(string $entityType): array
    {
        $fields = [];
        
        // Varlık tipine göre tablo adını belirle
        $tableName = $this->getTableNameFromEntity($entityType);
        
        if ($tableName && Schema::hasTable($tableName)) {
            // Tablodaki tüm kolonları al
            $columns = Schema::getColumnListing($tableName);
            
            foreach ($columns as $column) {
                $type = Schema::getColumnType($tableName, $column);
                
                $fields[$column] = [
                    'name' => $column,
                    'type' => $type,
                    'description' => $this->getFieldDescription($tableName, $column)
                ];
            }
            
            // İlişkileri ekle
            $relationFields = $this->getRelationFields($entityType);
            $fields = array_merge($fields, $relationFields);
        }
        
        // Sistem alanlarını alfabetik olarak sırala
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
        // Alan açıklamaları - ileride genişletilebilir
        $descriptions = [
            'rooms.id' => 'Oda ID',
            'rooms.hotel_id' => 'Otelin ID',
            'rooms.room_type_id' => 'Oda tipi ID',
            'rooms.name' => 'Oda adı',
            'rooms.code' => 'Oda kodu',
            'rooms.description' => 'Oda açıklaması',
            'rooms.capacity' => 'Maksimum kapasite',
            'rooms.base_occupancy' => 'Temel doluluk',
            'rooms.extra_bed' => 'İlave yatak kullanılabilir mi',
            'rooms.is_active' => 'Oda aktif mi',
            
            'rate_plans.id' => 'Fiyat planı ID',
            'rate_plans.room_id' => 'Oda ID',
            'rate_plans.name' => 'Fiyat planı adı',
            'rate_plans.code' => 'Fiyat planı kodu',
            'rate_plans.is_active' => 'Fiyat planı aktif mi',
            
            'inventories.id' => 'Envanter ID',
            'inventories.room_id' => 'Oda ID',
            'inventories.date' => 'Tarih',
            'inventories.available' => 'Müsait oda sayısı',
            
            'reservations.id' => 'Rezervasyon ID',
            'reservations.room_id' => 'Oda ID',
            'reservations.guest_id' => 'Misafir ID',
            'reservations.checkin_date' => 'Giriş tarihi',
            'reservations.checkout_date' => 'Çıkış tarihi',
            'reservations.adults' => 'Yetişkin sayısı',
            'reservations.children' => 'Çocuk sayısı',
            'reservations.rate_amount' => 'Toplam fiyat',
            'reservations.status' => 'Durum',
            
            'hotels.id' => 'Otel ID',
            'hotels.name' => 'Otel adı',
            'hotels.region_id' => 'Bölge ID',
            'hotels.description' => 'Otel açıklaması',
            'hotels.address' => 'Adres',
            'hotels.is_active' => 'Otel aktif mi',
            
            'guests.id' => 'Misafir ID',
            'guests.first_name' => 'Ad',
            'guests.last_name' => 'Soyad',
            'guests.email' => 'E-posta',
            'guests.phone' => 'Telefon'
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
        
        // Varlık tipine özel ilişki alanları
        switch ($entityType) {
            case 'room':
                $relations['hotel.name'] = [
                    'name' => 'hotel.name',
                    'type' => 'relation',
                    'description' => 'Otel adı'
                ];
                $relations['room_type.name'] = [
                    'name' => 'room_type.name',
                    'type' => 'relation',
                    'description' => 'Oda tipi adı'
                ];
                break;
                
            case 'reservation':
                $relations['guest.full_name'] = [
                    'name' => 'guest.full_name',
                    'type' => 'relation',
                    'description' => 'Misafir adı soyadı'
                ];
                $relations['room.name'] = [
                    'name' => 'room.name',
                    'type' => 'relation',
                    'description' => 'Oda adı'
                ];
                $relations['hotel.name'] = [
                    'name' => 'hotel.name',
                    'type' => 'relation',
                    'description' => 'Otel adı'
                ];
                break;
                
            // Diğer varlık tipleri için ilişkiler eklenebilir
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
     * @param string $type
     * @param string|null $description
     * @return XmlMapping
     */
    public function saveMapping(
        Channel $channel,
        string $entityType,
        string $name,
        array $mappingData,
        string $type = 'import',
        ?string $description = null
    ): XmlMapping {
        return XmlMapping::create([
            'channel_id' => $channel->id,
            'name' => $name,
            'type' => $type,
            'mapping_entity' => $entityType,
            'mapping_data' => $mappingData,
            'description' => $description,
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
            'TRY' => 'Türk Lirası',
            'USD' => 'ABD Doları',
            'EUR' => 'Euro',
            'GBP' => 'İngiliz Sterlini',
        ];
    }

    // saveMapping metodu kaldırıldı - artık DataMappingService sınıfındaki metodu kullanacağız
}