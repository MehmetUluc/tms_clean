<?php

namespace App\Plugins\Accommodation\Filament\Resources\RoomResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoom extends EditRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // JSON alanları için null yerine boş dizi kullan
        $data['child_policies'] = $data['child_policies'] ?? [];
        $data['features_details'] = $data['features_details'] ?? [];
        $data['gallery'] = $data['gallery'] ?? [];

        // Sayısal alanlar için varsayılan değerler
        $data['capacity_children'] = $data['capacity_children'] ?? 0;

        return $data;
    }
}