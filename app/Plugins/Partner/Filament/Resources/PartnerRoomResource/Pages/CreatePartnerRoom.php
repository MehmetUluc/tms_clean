<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerRoomResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerRoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePartnerRoom extends CreateRecord
{
    protected static string $resource = PartnerRoomResource::class;
    
    public function getTitle(): string
    {
        return 'Yeni Oda Ekle';
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}