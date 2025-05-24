<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerHotelResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerHotelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerHotel extends EditRecord
{
    protected static string $resource = PartnerHotelResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Otel Düzenle';
    }
}