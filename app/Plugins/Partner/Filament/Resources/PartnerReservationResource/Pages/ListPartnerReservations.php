<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerReservationResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerReservationResource;
use Filament\Resources\Pages\ListRecords;

class ListPartnerReservations extends ListRecords
{
    protected static string $resource = PartnerReservationResource::class;
    
    protected function getHeaderActions(): array
    {
        return [];
    }
    
    public function getTitle(): string
    {
        return 'Rezervasyonlarım';
    }
}