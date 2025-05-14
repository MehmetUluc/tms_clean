<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\HotelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotel extends EditRecord
{
    protected static string $resource = HotelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // JSON verisini ayrı form alanlarına doldur
        if (isset($data['check_in_out'])) {
            $checkInOut = is_string($data['check_in_out']) 
                ? json_decode($data['check_in_out'], true) 
                : ($data['check_in_out'] ?? []);
                
            $data['check_in_from'] = $checkInOut['check_in_from'] ?? '14:00';
            $data['check_in_until'] = $checkInOut['check_in_until'] ?? '23:59';
            $data['check_out_from'] = $checkInOut['check_out_from'] ?? '07:00';
            $data['check_out_until'] = $checkInOut['check_out_until'] ?? '12:00';
        }
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Zaman bilgilerini düzgün bir şekilde JSON olarak kaydet
        if (isset($data['check_in_from'], $data['check_in_until'], $data['check_out_from'], $data['check_out_until'])) {
            $data['check_in_out'] = json_encode([
                'check_in_from' => $data['check_in_from'],
                'check_in_until' => $data['check_in_until'],
                'check_out_from' => $data['check_out_from'],
                'check_out_until' => $data['check_out_until'],
            ]);

            // Artık kullanılmayan alanları kaldır
            unset($data['check_in_from']);
            unset($data['check_in_until']);
            unset($data['check_out_from']);
            unset($data['check_out_until']);
        }

        // Diğer JSON alanlarını düzgün bir şekilde string olarak kaydet
        if (isset($data['amenities']) && is_array($data['amenities'])) {
            $data['amenities'] = json_encode($data['amenities']);
        }

        if (isset($data['policies']) && is_array($data['policies'])) {
            $data['policies'] = json_encode($data['policies']);
        }

        if (isset($data['gallery']) && is_array($data['gallery'])) {
            $data['gallery'] = json_encode($data['gallery']);
        }

        // URL'den vendor_id geldiyse onu kullan
        if (request()->has('vendor_id') && request()->get('vendor_id')) {
            $data['vendor_id'] = request()->get('vendor_id');
        }

        // Artık default board type kullanılmıyor

        return $data;
    }

    // Default board type ayarlama işlemi kaldırıldı
}