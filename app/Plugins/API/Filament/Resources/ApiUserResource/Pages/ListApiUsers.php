<?php

namespace App\Plugins\API\Filament\Resources\ApiUserResource\Pages;

use App\Plugins\API\Filament\Resources\ApiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiUsers extends ListRecords
{
    protected static string $resource = ApiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}