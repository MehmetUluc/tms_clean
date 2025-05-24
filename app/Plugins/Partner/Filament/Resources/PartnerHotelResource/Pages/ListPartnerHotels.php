<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerHotelResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerHotelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerHotels extends ListRecords
{
    protected static string $resource = PartnerHotelResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Otellerim';
    }
}