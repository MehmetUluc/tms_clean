<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing permissions
        $newPermissions = [
            'view_own_rooms',
            'create_own_rooms',
            'update_own_rooms',
            'delete_own_rooms',
            'manage_own_staff', // This covers view, create, update, delete for staff
        ];
        
        foreach ($newPermissions as $permission) {
            Permission::findOrCreate($permission);
        }
        
        // Assign to partner role
        $partnerRole = Role::findByName('partner');
        if ($partnerRole) {
            $partnerRole->givePermissionTo($newPermissions);
        }
        
        // Also give limited permissions to partner_staff role
        $partnerStaffRole = Role::findByName('partner_staff');
        if ($partnerStaffRole) {
            $partnerStaffRole->givePermissionTo([
                'view_own_rooms',
                'update_own_rooms',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'view_own_rooms',
            'create_own_rooms',
            'update_own_rooms',
            'delete_own_rooms',
            'manage_own_staff',
        ];
        
        foreach ($permissions as $permission) {
            $perm = Permission::findByName($permission);
            if ($perm) {
                $perm->delete();
            }
        }
    }
};