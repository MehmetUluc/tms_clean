<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Otel modülü izinleri
        $hotelPermissions = [
            'view_hotels',
            'create_hotels',
            'edit_hotels',
            'delete_hotels',
        ];

        // Oda modülü izinleri
        $roomPermissions = [
            'view_rooms',
            'create_rooms',
            'edit_rooms',
            'delete_rooms',
        ];

        // Rezervasyon modülü izinleri
        $reservationPermissions = [
            'view_reservations',
            'create_reservations',
            'edit_reservations',
            'delete_reservations',
            'confirm_reservations',
            'cancel_reservations',
        ];

        // Acente modülü izinleri
        $agencyPermissions = [
            'view_agencies',
            'create_agencies',
            'edit_agencies',
            'delete_agencies',
            'manage_agency_credits',
        ];

        // Transfer modülü izinleri
        $transferPermissions = [
            'view_transfers',
            'create_transfers',
            'edit_transfers',
            'delete_transfers',
        ];

        // Kullanıcı yönetimi izinleri
        $userPermissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
        ];

        // Tüm izinleri birleştir
        $allPermissions = array_merge(
            $hotelPermissions,
            $roomPermissions,
            $reservationPermissions,
            $agencyPermissions,
            $transferPermissions,
            $userPermissions
        );

        // İzinleri oluştur
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Süper Admin rolü - Tüm izinlere sahip
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Otel Yöneticisi rolü
        $hotelManagerRole = Role::create(['name' => 'hotel-manager']);
        $hotelManagerRole->givePermissionTo([
            ...$hotelPermissions,
            ...$roomPermissions,
            'view_reservations',
            'edit_reservations',
            'confirm_reservations',
            'cancel_reservations',
        ]);

        // Rezervasyon Görevlisi rolü
        $reservationAgentRole = Role::create(['name' => 'reservation-agent']);
        $reservationAgentRole->givePermissionTo([
            'view_hotels',
            'view_rooms',
            ...$reservationPermissions,
        ]);

        // Acente Yöneticisi rolü
        $agencyManagerRole = Role::create(['name' => 'agency-manager']);
        $agencyManagerRole->givePermissionTo([
            'view_hotels',
            'view_rooms',
            'create_reservations',
            'view_reservations',
            'edit_reservations',
            'cancel_reservations',
        ]);

        // Admin kullanıcısını bul ve super-admin rolünü ata
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->assignRole('super-admin');
        }
    }
}