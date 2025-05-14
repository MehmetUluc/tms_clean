<?php

namespace App\Plugins\Vendor\Filament\Resources\VendorResource\Pages;

use App\Plugins\Vendor\Filament\Resources\VendorResource;
use App\Plugins\Vendor\Services\VendorService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $vendorService = app(VendorService::class);
        
        $vendor = $vendorService->createVendor($data);
        
        // Assign vendor role to user
        $user = $vendor->user;
        $vendorRole = \Spatie\Permission\Models\Role::findByName('vendor');
        $user->assignRole($vendorRole);
        
        Notification::make()
            ->title('Vendor created successfully')
            ->success()
            ->send();
            
        return $vendor;
    }
}