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
        // Add room types permissions
        $permissions = [
            'view_room_types',
            'create_room_types',
            'update_room_types',
            'delete_room_types',
        ];
        
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }
        
        // Give to appropriate roles
        $superAdminRole = Role::findByName('super_admin');
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
        
        $adminRole = Role::findByName('admin');
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
        
        $hotelManagerRole = Role::findByName('hotel_manager');
        if ($hotelManagerRole) {
            $hotelManagerRole->givePermissionTo([
                'view_room_types',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'view_room_types',
            'create_room_types',
            'update_room_types',
            'delete_room_types',
        ];
        
        foreach ($permissions as $permission) {
            $perm = Permission::findByName($permission);
            if ($perm) {
                $perm->delete();
            }
        }
    }
};