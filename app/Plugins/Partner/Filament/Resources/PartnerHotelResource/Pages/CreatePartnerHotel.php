<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerHotelResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerHotelResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerHotel extends CreateRecord
{
    protected static string $resource = PartnerHotelResource::class;
    
    public function getTitle(): string
    {
        return 'Yeni Otel Ekle';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}