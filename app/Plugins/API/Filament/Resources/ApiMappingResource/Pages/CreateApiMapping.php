<?php

namespace App\Plugins\API\Filament\Resources\ApiMappingResource\Pages;

use App\Plugins\API\Filament\Resources\ApiMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApiMapping extends CreateRecord
{
    protected static string $resource = ApiMappingResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Endpoint path boş bırakıldıysa otomatik oluştur
        if (empty($data['endpoint_path'])) {
            $slug = \Illuminate\Support\Str::slug($data['name']);
            $uniqueId = \Illuminate\Support\Str::random(8);
            $data['endpoint_path'] = "/api/v1/{$slug}-{$uniqueId}";
        }

        // Eğer endpointin başında / yoksa ekle
        if (!str_starts_with($data['endpoint_path'], '/')) {
            $data['endpoint_path'] = '/' . $data['endpoint_path'];
        }
        
        return $data;
    }
}