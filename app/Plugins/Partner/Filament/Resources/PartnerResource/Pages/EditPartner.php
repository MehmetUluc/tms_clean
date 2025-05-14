<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerResource;
use App\Plugins\Partner\Services\PartnerService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPartner extends EditRecord
{
    protected static string $resource = PartnerResource::class;

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
                        ->title('Partner activated successfully')
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
                        ->title('Partner deactivated successfully')
                        ->success()
                        ->send();
                }),
        ];
    }
}