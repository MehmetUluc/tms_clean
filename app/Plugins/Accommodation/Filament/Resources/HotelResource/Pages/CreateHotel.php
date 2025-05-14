<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\HotelResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHotel extends CreateRecord
{
    protected static string $resource = HotelResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
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

        // Tenant ID ekleniyor - Plugin mimarisinde çoklu kiracı (tenant) desteği için
        if (auth()->check() && auth()->user()->tenant_id) {
            $data['tenant_id'] = auth()->user()->tenant_id;
        }

        // Eğer kullanıcı vendor rolüne sahipse, vendor_id'yi otomatik olarak ayarla
        if (auth()->check() && auth()->user()->hasRole('vendor')) {
            $vendorService = app(\App\Plugins\Vendor\Services\VendorService::class);
            $vendor = $vendorService->getVendorForUser(auth()->user());

            if ($vendor) {
                $data['vendor_id'] = $vendor->id;
            }
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