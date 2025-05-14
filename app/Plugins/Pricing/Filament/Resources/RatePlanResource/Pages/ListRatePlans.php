<?php

namespace App\Plugins\Pricing\Filament\Resources\RatePlanResource\Pages;

use App\Plugins\Pricing\Filament\Resources\RatePlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRatePlans extends ListRecords
{
    protected static string $resource = RatePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}