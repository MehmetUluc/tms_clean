<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerStaffResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerStaffResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class CreatePartnerStaff extends CreateRecord
{
    protected static string $resource = PartnerStaffResource::class;
    
    public function getTitle(): string
    {
        return 'Yeni Editör Ekle';
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set is_active field if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        $partner = Auth::user()->partner;
        
        if ($partner) {
            // Add user to partner's staff list
            $staffIds = $partner->staff_user_ids ?? [];
            $staffIds[] = $this->record->id;
            $partner->update(['staff_user_ids' => array_unique($staffIds)]);
            
            // Assign role based on form selection
            $roleValue = $this->data['role'] ?? 'partner_staff';
            $role = Role::findByName($roleValue);
            
            if ($role) {
                $this->record->assignRole($role);
            }
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}