<?php

namespace App\Plugins\Accommodation\Filament\Resources\BoardTypeResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\BoardTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBoardTypes extends ListRecords
{
    protected static string $resource = BoardTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}