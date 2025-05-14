<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewPartner extends ViewRecord
{
    protected static string $resource = PartnerResource::class;

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