<?php

namespace App\Plugins\Vendor\Filament\Resources\VendorResource\Pages;

use App\Plugins\Vendor\Filament\Resources\VendorResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('viewHotels')
                ->label('View Hotels')
                ->icon('heroicon-o-building-office-2')
                ->url(fn () => static::getResource()::getUrl('view-hotels', ['record' => $this->record])),
            
            Actions\Action::make('manageCommission')
                ->label('Manage Commission')
                ->icon('heroicon-o-currency-dollar')
                ->url(fn () => static::getResource()::getUrl('manage-commission', ['record' => $this->record])),
            
            Actions\Action::make('viewFinancials')
                ->label('View Financials')
                ->icon('heroicon-o-banknotes')
                ->url(fn () => static::getResource()::getUrl('view-financials', ['record' => $this->record])),
        ];
    }
}