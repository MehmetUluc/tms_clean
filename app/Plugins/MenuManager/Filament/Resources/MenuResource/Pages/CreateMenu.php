<?php

namespace App\Plugins\MenuManager\Filament\Resources\MenuResource\Pages;

use App\Plugins\MenuManager\Filament\Resources\MenuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}