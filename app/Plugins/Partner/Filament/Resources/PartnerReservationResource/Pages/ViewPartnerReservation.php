<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerReservationResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPartnerReservation extends ViewRecord
{
    protected static string $resource = PartnerReservationResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Rezervasyon Detayları';
    }
}