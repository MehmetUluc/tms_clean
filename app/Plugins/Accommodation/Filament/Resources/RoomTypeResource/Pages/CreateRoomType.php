<?php

namespace App\Plugins\Accommodation\Filament\Resources\RoomTypeResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRoomType extends CreateRecord
{
    protected static string $resource = RoomTypeResource::class;
}