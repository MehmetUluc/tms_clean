<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddShieldCustomPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Filament Shield'a özgü izinleri ekle
        $permissionNames = [
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
            
            'assign_roles', // Rolleri atama izni - eksik olan bu
        ];
        
        $now = now()->format('Y-m-d H:i:s');
        
        foreach ($permissionNames as $name) {
            // İzin zaten varsa atla
            $exists = DB::table('permissions')->where('name', $name)->exists();
            
            if (!$exists) {
                DB::table('permissions')->insert([
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                
                $this->command->info("'$name' izni eklendi");
            }
        }
        
        // Admin rolüne bu izinleri ekle
        $role = Role::where('name', 'super-admin')->first();
        
        if ($role) {
            $permissions = Permission::whereIn('name', $permissionNames)->get();
            
            foreach ($permissions as $permission) {
                if (!DB::table('role_has_permissions')
                    ->where('permission_id', $permission->id)
                    ->where('role_id', $role->id)
                    ->exists()
                ) {
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                    ]);
                    
                    $this->command->info("'$permission->name' izni super-admin rolüne atandı");
                }
            }
        }
    }
}