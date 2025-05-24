<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerRoomResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerRoom extends EditRecord
{
    protected static string $resource = PartnerRoomResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Oda Düzenle';
    }
}