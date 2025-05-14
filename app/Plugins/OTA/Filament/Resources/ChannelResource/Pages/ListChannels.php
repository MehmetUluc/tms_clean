<?php

namespace App\Plugins\OTA\Filament\Resources\ChannelResource\Pages;

use App\Plugins\OTA\Filament\Resources\ChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChannels extends ListRecords
{
    protected static string $resource = ChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}