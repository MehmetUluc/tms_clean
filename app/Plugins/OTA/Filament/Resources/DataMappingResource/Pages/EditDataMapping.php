<?php

namespace App\Plugins\OTA\Filament\Resources\DataMappingResource\Pages;

use App\Plugins\OTA\Filament\Resources\DataMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataMapping extends EditRecord
{
    protected static string $resource = DataMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
            Actions\Action::make('go_to_builder')
                ->label('Eşleştirme Düzenleyici')
                ->icon('heroicon-o-document-text')
                ->url(fn () => static::getResource()::getUrl('builder', ['record' => $this->record]))
                ->color('info'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}