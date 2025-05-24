<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerStaffResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnerStaff extends ListRecords
{
    protected static string $resource = PartnerStaffResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Editör Ekle'),
        ];
    }
    
    public function getTitle(): string
    {
        return 'Editör Yönetimi';
    }
}