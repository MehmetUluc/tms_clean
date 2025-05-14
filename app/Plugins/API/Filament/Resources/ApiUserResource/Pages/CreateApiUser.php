<?php

namespace App\Plugins\API\Filament\Resources\ApiUserResource\Pages;

use App\Plugins\API\Filament\Resources\ApiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApiUser extends CreateRecord
{
    protected static string $resource = ApiUserResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // API anahtarı boş bırakıldıysa otomatik oluştur
        if (empty($data['api_key'])) {
            $data['api_key'] = \Illuminate\Support\Str::random(64);
        }
        
        return $data;
    }
}