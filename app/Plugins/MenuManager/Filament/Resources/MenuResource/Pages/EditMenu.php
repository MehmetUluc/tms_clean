<?php

namespace App\Plugins\MenuManager\Filament\Resources\MenuResource\Pages;

use App\Plugins\MenuManager\Filament\Resources\MenuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('build')
                ->label('Build Menu')
                ->icon('heroicon-o-pencil-square')
                ->url(fn () => $this->getResource()::getUrl('build', ['record' => $this->getRecord()])),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}