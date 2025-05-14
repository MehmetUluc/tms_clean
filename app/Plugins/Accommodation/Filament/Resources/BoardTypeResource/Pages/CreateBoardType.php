<?php

namespace App\Plugins\Accommodation\Filament\Resources\BoardTypeResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\BoardTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBoardType extends CreateRecord
{
    protected static string $resource = BoardTypeResource::class;
}