<?php

namespace App\Plugins\API\Filament\Resources\ApiMappingResource\Pages;

use App\Plugins\API\Filament\Resources\ApiMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiMappings extends ListRecords
{
    protected static string $resource = ApiMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('xml_mapper')
                ->label('XML/JSON Analiz Aracı')
                ->icon('heroicon-m-code-bracket')
                ->color('primary')
                ->url(route('xml-mapper.index'))
                ->openUrlInNewTab(),
        ];
    }
}