<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerResource;
use App\Plugins\Partner\Services\PartnerService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreatePartner extends CreateRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $partnerService = app(PartnerService::class);

        $partner = $partnerService->createPartner($data);
        
        // Assign partner role to user
        $user = $partner->user;
        $partnerRole = \Spatie\Permission\Models\Role::findByName('partner');
        $user->assignRole($partnerRole);
        
        Notification::make()
            ->title('Partner created successfully')
            ->success()
            ->send();
            
        return $partner;
    }
}