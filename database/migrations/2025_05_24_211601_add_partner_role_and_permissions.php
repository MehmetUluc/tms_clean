<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create partner-specific permissions
        $permissions = [
            // Partner profile management
            'view_own_partner_profile',
            'update_own_partner_profile',
            'view_partner_onboarding',
            'complete_partner_onboarding',
            
            // Hotel management (limited to their hotels)
            'view_own_hotels',
            'create_own_hotels',
            'update_own_hotels',
            'delete_own_hotels',
            
            // Pricing management
            'view_own_pricing',
            'create_own_pricing',
            'update_own_pricing',
            'delete_own_pricing',
            
            // Reservation management
            'view_own_reservations',
            'update_own_reservations',
            
            // Financial permissions
            'view_own_financial_summary',
            'view_own_transactions',
            'view_own_payments',
            'create_payment_request',
            'view_own_payment_requests',
            
            // Document permissions
            'view_own_documents',
            'upload_own_documents',
            'delete_own_documents',
            
            // Ministry reports
            'view_own_ministry_reports',
            'submit_ministry_reports',
            
            // Staff management
            'view_own_staff',
            'create_own_staff',
            'update_own_staff',
            'delete_own_staff',
            
            // Dashboard access
            'access_partner_dashboard',
            'access_partner_panel',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // Create partner role
        $partnerRole = Role::firstOrCreate(['name' => 'partner', 'guard_name' => 'web']);
        
        // Assign permissions to partner role
        $partnerRole->syncPermissions($permissions);
        
        // Create partner staff role (for editors added by partners)
        $partnerStaffRole = Role::firstOrCreate(['name' => 'partner_staff', 'guard_name' => 'web']);
        
        // Staff permissions (limited subset)
        $staffPermissions = [
            'view_own_hotels',
            'update_own_hotels',
            'view_own_pricing',
            'update_own_pricing',
            'view_own_reservations',
            'access_partner_dashboard',
        ];
        
        $partnerStaffRole->syncPermissions($staffPermissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove roles
        Role::where('name', 'partner')->delete();
        Role::where('name', 'partner_staff')->delete();
        
        // Remove permissions
        $permissions = [
            'view_own_partner_profile',
            'update_own_partner_profile',
            'view_partner_onboarding',
            'complete_partner_onboarding',
            'view_own_hotels',
            'create_own_hotels',
            'update_own_hotels',
            'delete_own_hotels',
            'view_own_pricing',
            'create_own_pricing',
            'update_own_pricing',
            'delete_own_pricing',
            'view_own_reservations',
            'update_own_reservations',
            'view_own_financial_summary',
            'view_own_transactions',
            'view_own_payments',
            'create_payment_request',
            'view_own_payment_requests',
            'view_own_documents',
            'upload_own_documents',
            'delete_own_documents',
            'view_own_ministry_reports',
            'submit_ministry_reports',
            'view_own_staff',
            'create_own_staff',
            'update_own_staff',
            'delete_own_staff',
            'access_partner_dashboard',
            'access_partner_panel',
        ];
        
        Permission::whereIn('name', $permissions)->delete();
    }
};
