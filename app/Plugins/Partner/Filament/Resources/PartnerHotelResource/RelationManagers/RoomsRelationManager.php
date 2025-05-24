<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerHotelResource\RelationManagers;

use App\Plugins\Accommodation\Filament\Resources\HotelResource\RelationManagers\RoomsRelationManager as BaseRoomsRelationManager;

class RoomsRelationManager extends BaseRoomsRelationManager
{
    protected static string $relationship = 'rooms';
    
    protected static ?string $title = 'Odalar';
}