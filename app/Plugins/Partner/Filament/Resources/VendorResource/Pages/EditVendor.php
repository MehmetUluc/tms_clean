<?php

namespace App\Plugins\Vendor\Filament\Resources\VendorResource\Pages;

use App\Plugins\Vendor\Filament\Resources\VendorResource;
use App\Plugins\Vendor\Services\VendorService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditVendor extends EditRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
            Actions\Action::make('activate')
                ->label('Activate')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status !== 'active')
                ->action(function () {
                    $this->record->update(['status' => 'active']);
                    
                    Notification::make()
                        ->title('Vendor activated successfully')
                        ->success()
                        ->send();
                }),
                
            Actions\Action::make('deactivate')
                ->label('Deactivate')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'active')
                ->action(function () {
                    $this->record->update(['status' => 'inactive']);
                    
                    Notification::make()
                        ->title('Vendor deactivated successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}