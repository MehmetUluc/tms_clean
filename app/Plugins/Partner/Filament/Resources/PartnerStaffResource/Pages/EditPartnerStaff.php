<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerStaffResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class EditPartnerStaff extends EditRecord
{
    protected static string $resource = PartnerStaffResource::class;
    
    protected function getHeaderActions(): array
    {
        $actions = [];
        
        // Don't allow deleting the partner owner
        if ($this->record->id !== Auth::user()->getAssociatedPartner()->user_id) {
            $actions[] = Actions\DeleteAction::make()
                ->before(function () {
                    // Remove from partner's staff list
                    $partner = Auth::user()->getAssociatedPartner();
                    if ($partner) {
                        $staffIds = $partner->staff_user_ids ?? [];
                        $staffIds = array_diff($staffIds, [$this->record->id]);
                        $partner->update(['staff_user_ids' => array_values($staffIds)]);
                    }
                });
        }
        
        return $actions;
    }
    
    public function getTitle(): string
    {
        return 'Editör Düzenle';
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Don't change role for partner owner
        if ($this->record->id === Auth::user()->getAssociatedPartner()->user_id) {
            unset($data['role']);
        }
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Update role if changed and not partner owner
        if (isset($this->data['role']) && $this->record->id !== Auth::user()->getAssociatedPartner()->user_id) {
            // Remove all existing partner roles
            $this->record->removeRole('partner');
            $this->record->removeRole('partner_staff');
            
            // Assign new role
            $role = Role::findByName($this->data['role']);
            if ($role) {
                $this->record->assignRole($role);
            }
        }
    }
}