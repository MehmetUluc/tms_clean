<?php

namespace App\Plugins\Accommodation\Filament\Resources\BoardTypeResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\BoardTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBoardType extends EditRecord
{
    protected static string $resource = BoardTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}