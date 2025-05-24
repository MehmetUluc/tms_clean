<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerReservationResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerReservation extends EditRecord
{
    protected static string $resource = PartnerReservationResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Rezervasyon Düzenle';
    }
}