<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super_admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Make sure user with ID 1 exists
        $user = User::find(1);
        
        if ($user) {
            // Assign super_admin role to user
            $user->assignRole($superAdminRole);
        }
        
        // Create new permissions
        $allPermissions = [];
        
        // Get all existing permissions and create any that don't exist
        $allPermissionNames = Permission::pluck('name')->toArray();
        
        // Create administrator user bypass to all permissions
        // Create bypass gate in AuthServiceProvider
    }
}