<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelTagResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\HotelTagResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHotelTag extends CreateRecord
{
    protected static string $resource = HotelTagResource::class;
}