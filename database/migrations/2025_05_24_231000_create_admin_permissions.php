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
        // Admin permissions
        $adminPermissions = [
            // Hotel management
            'view_hotels',
            'create_hotels',
            'update_hotels',
            'delete_hotels',
            
            // Room management
            'view_rooms',
            'create_rooms',
            'update_rooms',
            'delete_rooms',
            
            // Reservation management
            'view_reservations',
            'create_reservations',
            'update_reservations',
            'delete_reservations',
            
            // Rate plan management
            'view_rate_plans',
            'create_rate_plans',
            'update_rate_plans',
            'delete_rate_plans',
            
            // User management
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            
            // Partner management
            'view_partners',
            'create_partners',
            'update_partners',
            'delete_partners',
            
            // Region management
            'view_regions',
            'create_regions',
            'update_regions',
            'delete_regions',
            
            // Hotel types
            'view_hotel_types',
            'create_hotel_types',
            'update_hotel_types',
            'delete_hotel_types',
            
            // Board types
            'view_board_types',
            'create_board_types',
            'update_board_types',
            'delete_board_types',
            
            // Amenities
            'view_amenities',
            'create_amenities',
            'update_amenities',
            'delete_amenities',
            
            // Reporting
            'view_reports',
            'export_reports',
            
            // System settings
            'view_settings',
            'update_settings',
            
            // Integration/API
            'view_api_settings',
            'manage_api_settings',
            
            // Theme management
            'view_themes',
            'manage_themes',
        ];
        
        // Create permissions
        foreach ($adminPermissions as $permission) {
            Permission::findOrCreate($permission);
        }
        
        // Create super admin role if not exists
        $superAdminRole = Role::findOrCreate('super_admin');
        
        // Give all permissions to super admin
        $superAdminRole->givePermissionTo(Permission::all());
        
        // Create admin role if not exists
        $adminRole = Role::findOrCreate('admin');
        
        // Give most permissions to admin (excluding user and partner management)
        $adminRole->givePermissionTo([
            'view_hotels',
            'create_hotels',
            'update_hotels',
            'delete_hotels',
            'view_rooms',
            'create_rooms',
            'update_rooms',
            'delete_rooms',
            'view_reservations',
            'create_reservations',
            'update_reservations',
            'delete_reservations',
            'view_rate_plans',
            'create_rate_plans',
            'update_rate_plans',
            'delete_rate_plans',
            'view_regions',
            'create_regions',
            'update_regions',
            'delete_regions',
            'view_hotel_types',
            'create_hotel_types',
            'update_hotel_types',
            'delete_hotel_types',
            'view_board_types',
            'create_board_types',
            'update_board_types',
            'delete_board_types',
            'view_amenities',
            'create_amenities',
            'update_amenities',
            'delete_amenities',
            'view_reports',
            'export_reports',
            'view_themes',
            'manage_themes',
        ]);
        
        // Create hotel manager role if not exists
        $hotelManagerRole = Role::findOrCreate('hotel_manager');
        
        // Give limited permissions to hotel manager
        $hotelManagerRole->givePermissionTo([
            'view_hotels',
            'update_hotels',
            'view_rooms',
            'create_rooms',
            'update_rooms',
            'delete_rooms',
            'view_reservations',
            'update_reservations',
            'view_rate_plans',
            'create_rate_plans',
            'update_rate_plans',
            'delete_rate_plans',
            'view_reports',
            'export_reports',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all permissions created in this migration
        $permissions = [
            'view_hotels', 'create_hotels', 'update_hotels', 'delete_hotels',
            'view_rooms', 'create_rooms', 'update_rooms', 'delete_rooms',
            'view_reservations', 'create_reservations', 'update_reservations', 'delete_reservations',
            'view_rate_plans', 'create_rate_plans', 'update_rate_plans', 'delete_rate_plans',
            'view_users', 'create_users', 'update_users', 'delete_users',
            'view_partners', 'create_partners', 'update_partners', 'delete_partners',
            'view_regions', 'create_regions', 'update_regions', 'delete_regions',
            'view_hotel_types', 'create_hotel_types', 'update_hotel_types', 'delete_hotel_types',
            'view_board_types', 'create_board_types', 'update_board_types', 'delete_board_types',
            'view_amenities', 'create_amenities', 'update_amenities', 'delete_amenities',
            'view_reports', 'export_reports',
            'view_settings', 'update_settings',
            'view_api_settings', 'manage_api_settings',
            'view_themes', 'manage_themes',
        ];
        
        // Delete permissions
        foreach ($permissions as $permission) {
            $perm = Permission::findByName($permission);
            if ($perm) {
                $perm->delete();
            }
        }
    }
};