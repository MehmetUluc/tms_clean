<?php

namespace App\Plugins\API\Filament\Resources\ApiMappingResource\Pages;

use App\Plugins\API\Filament\Resources\ApiMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiMapping extends EditRecord
{
    protected static string $resource = ApiMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}