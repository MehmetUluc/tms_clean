<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerHotelResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerHotelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPartnerHotel extends ViewRecord
{
    protected static string $resource = PartnerHotelResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Otel Detayları';
    }
}