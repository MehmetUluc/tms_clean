<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerRoomResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerRooms extends ListRecords
{
    protected static string $resource = PartnerRoomResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Odalarım';
    }
}