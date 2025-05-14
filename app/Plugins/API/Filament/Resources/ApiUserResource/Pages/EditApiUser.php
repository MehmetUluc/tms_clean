<?php

namespace App\Plugins\API\Filament\Resources\ApiUserResource\Pages;

use App\Plugins\API\Filament\Resources\ApiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiUser extends EditRecord
{
    protected static string $resource = ApiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}