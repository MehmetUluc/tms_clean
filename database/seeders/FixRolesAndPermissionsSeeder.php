<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class FixRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin kullanıcısını oluştur veya güncelle
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );
        
        // Temel izinleri direct SQL ile ekle
        $this->insertPermissionsWithSql();
        
        // Rolleri direct SQL ile ekle
        $this->insertRolesWithSql();
        
        // Admin kullanıcısına super-admin rolü ata
        $this->assignAdminRoleWithSql($admin->id);
    }
    
    /**
     * Temel izinleri SQL ile ekle
     */
    private function insertPermissionsWithSql(): void
    {
        // Önce tüm mevcut verileri temizle
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // model_has_roles tablosunu temizlemeye çalış
        try {
            if (Schema::hasTable('model_has_roles')) {
                DB::statement('TRUNCATE TABLE model_has_roles');
            }
        } catch (\Exception $e) {
            // Tablo henüz yoksa geç
        }
        
        // model_has_permissions tablosunu temizlemeye çalış
        try {
            if (Schema::hasTable('model_has_permissions')) {
                DB::statement('TRUNCATE TABLE model_has_permissions');
            }
        } catch (\Exception $e) {
            // Tablo henüz yoksa geç
        }
        
        // role_has_permissions tablosunu temizlemeye çalış
        try {
            if (Schema::hasTable('role_has_permissions')) {
                DB::statement('TRUNCATE TABLE role_has_permissions');
            }
        } catch (\Exception $e) {
            // Tablo henüz yoksa geç
        }
        
        // roles tablosunu temizlemeye çalış
        try {
            if (Schema::hasTable('roles')) {
                DB::statement('TRUNCATE TABLE roles');
            }
        } catch (\Exception $e) {
            // Tablo henüz yoksa geç
        }
        
        // permissions tablosunu temizlemeye çalış
        try {
            if (Schema::hasTable('permissions')) {
                DB::statement('TRUNCATE TABLE permissions');
            }
        } catch (\Exception $e) {
            // Tablo henüz yoksa geç
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Permissions tablosunu oluştur
        DB::statement("CREATE TABLE IF NOT EXISTS permissions (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");
        
        // İzinleri ekle
        $permissions = [
            // Filament Shield Resource izinleri
            ['name' => 'view_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'view_any_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'create_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'update_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'restore_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'restore_any_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'replicate_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'reorder_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'delete_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'delete_any_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'force_delete_hotel::hotel', 'guard_name' => 'web'],
            ['name' => 'force_delete_any_hotel::hotel', 'guard_name' => 'web'],
            
            ['name' => 'view_hotel::region', 'guard_name' => 'web'],
            ['name' => 'view_any_hotel::region', 'guard_name' => 'web'],
            ['name' => 'create_hotel::region', 'guard_name' => 'web'],
            ['name' => 'update_hotel::region', 'guard_name' => 'web'],
            ['name' => 'restore_hotel::region', 'guard_name' => 'web'],
            ['name' => 'restore_any_hotel::region', 'guard_name' => 'web'],
            ['name' => 'replicate_hotel::region', 'guard_name' => 'web'],
            ['name' => 'reorder_hotel::region', 'guard_name' => 'web'],
            ['name' => 'delete_hotel::region', 'guard_name' => 'web'],
            ['name' => 'delete_any_hotel::region', 'guard_name' => 'web'],
            ['name' => 'force_delete_hotel::region', 'guard_name' => 'web'],
            ['name' => 'force_delete_any_hotel::region', 'guard_name' => 'web'],
            
            ['name' => 'view_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'view_any_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'create_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'update_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'restore_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'restore_any_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'replicate_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'reorder_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'delete_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'delete_any_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'force_delete_hotel::hotel-tag', 'guard_name' => 'web'],
            ['name' => 'force_delete_any_hotel::hotel-tag', 'guard_name' => 'web'],
            
            ['name' => 'view_user', 'guard_name' => 'web'],
            ['name' => 'view_any_user', 'guard_name' => 'web'],
            ['name' => 'create_user', 'guard_name' => 'web'],
            ['name' => 'update_user', 'guard_name' => 'web'],
            ['name' => 'restore_user', 'guard_name' => 'web'],
            ['name' => 'restore_any_user', 'guard_name' => 'web'],
            ['name' => 'replicate_user', 'guard_name' => 'web'],
            ['name' => 'reorder_user', 'guard_name' => 'web'],
            ['name' => 'delete_user', 'guard_name' => 'web'],
            ['name' => 'delete_any_user', 'guard_name' => 'web'],
            ['name' => 'force_delete_user', 'guard_name' => 'web'],
            ['name' => 'force_delete_any_user', 'guard_name' => 'web'],
            
            // Filament Shield panel izinleri
            ['name' => 'view_admin_panel', 'guard_name' => 'web'],

            // Eski izinleri de tutalım
            ['name' => 'manage_users', 'guard_name' => 'web'],
            ['name' => 'manage_roles', 'guard_name' => 'web'],
            ['name' => 'manage_permissions', 'guard_name' => 'web'],
            
            ['name' => 'view_hotels', 'guard_name' => 'web'],
            ['name' => 'create_hotels', 'guard_name' => 'web'],
            ['name' => 'edit_hotels', 'guard_name' => 'web'],
            ['name' => 'delete_hotels', 'guard_name' => 'web'],
            
            ['name' => 'view_rooms', 'guard_name' => 'web'],
            ['name' => 'create_rooms', 'guard_name' => 'web'],
            ['name' => 'edit_rooms', 'guard_name' => 'web'],
            ['name' => 'delete_rooms', 'guard_name' => 'web'],
            
            ['name' => 'view_reservations', 'guard_name' => 'web'],
            ['name' => 'create_reservations', 'guard_name' => 'web'],
            ['name' => 'edit_reservations', 'guard_name' => 'web'],
            ['name' => 'delete_reservations', 'guard_name' => 'web'],
            ['name' => 'confirm_reservations', 'guard_name' => 'web'],
            ['name' => 'cancel_reservations', 'guard_name' => 'web'],
            
            ['name' => 'view_agencies', 'guard_name' => 'web'],
            ['name' => 'create_agencies', 'guard_name' => 'web'],
            ['name' => 'edit_agencies', 'guard_name' => 'web'],
            ['name' => 'delete_agencies', 'guard_name' => 'web'],
            
            ['name' => 'view_transfers', 'guard_name' => 'web'],
            ['name' => 'create_transfers', 'guard_name' => 'web'],
            ['name' => 'edit_transfers', 'guard_name' => 'web'],
            ['name' => 'delete_transfers', 'guard_name' => 'web'],
        ];
        
        $now = now()->format('Y-m-d H:i:s');
        
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
    
    /**
     * Rolleri SQL ile ekle
     */
    private function insertRolesWithSql(): void
    {
        // Roles tablosunu oluştur
        DB::statement("CREATE TABLE IF NOT EXISTS roles (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )");
        
        // Rolleri ekle
        $roles = [
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['name' => 'hotel-manager', 'guard_name' => 'web'],
            ['name' => 'reservation-agent', 'guard_name' => 'web'],
            ['name' => 'agency-manager', 'guard_name' => 'web'],
        ];
        
        $now = now()->format('Y-m-d H:i:s');
        
        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        
        // Role has permissions tablosunu oluştur
        DB::statement("CREATE TABLE IF NOT EXISTS role_has_permissions (
            permission_id BIGINT UNSIGNED NOT NULL,
            role_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (permission_id, role_id)
        )");
        
        // Super admin rolü için tüm izinleri ekle
        $superAdminRoleId = DB::table('roles')->where('name', 'super-admin')->value('id');
        $allPermissionIds = DB::table('permissions')->pluck('id')->toArray();
        
        foreach ($allPermissionIds as $permissionId) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id' => $superAdminRoleId,
            ]);
        }
        
        // Diğer roller için daha az izin ata
        // Bu kısmı şimdilik basit tutuyoruz
    }
    
    /**
     * Admin kullanıcısına super-admin rolünü ata
     */
    private function assignAdminRoleWithSql(int $adminId): void
    {
        // Model has roles tablosunu oluştur
        DB::statement("CREATE TABLE IF NOT EXISTS model_has_roles (
            role_id BIGINT UNSIGNED NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (role_id, model_id, model_type)
        )");
        
        // Super admin rolünü bul
        $superAdminRoleId = DB::table('roles')->where('name', 'super-admin')->value('id');
        
        // Rol atamasını ekle
        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRoleId,
            'model_type' => 'App\\Models\\User',
            'model_id' => $adminId,
        ]);
    }
}