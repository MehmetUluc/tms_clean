<?php

namespace App\Plugins\Pricing\Filament\Resources\RatePlanResource\Pages;

use App\Plugins\Pricing\Filament\Resources\RatePlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRatePlan extends EditRecord
{
    protected static string $resource = RatePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
            Actions\Action::make('manage_prices')
                ->label('Fiyatları Yönet')
                ->icon('heroicon-o-banknotes')
                ->url(fn () => $this->getResource()::getUrl('manage-prices', ['record' => $this->record]))
                ->color('success'),
                
            Actions\Action::make('manage_inventory')
                ->label('Kontenjan Yönet')
                ->icon('heroicon-o-calendar')
                ->url(fn () => $this->getResource()::getUrl('manage-inventory', ['record' => $this->record]))
                ->color('warning'),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Add current user ID
        $data['updated_by'] = auth()->id();
        
        return $data;
    }
}