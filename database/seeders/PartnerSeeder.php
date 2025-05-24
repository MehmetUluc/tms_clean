<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Plugins\Partner\Models\Partner;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Partner rolünün var olduğundan emin ol
        $partnerRole = Role::firstOrCreate(['name' => 'partner', 'guard_name' => 'web']);
        
        // Test partner kullanıcısı oluştur
        $user = User::firstOrCreate([
            'email' => 'partner@example.com'
        ], [
            'name' => 'Test Partner',
            'password' => Hash::make('password'),
        ]);
        
        // Partner rolünü ata
        $user->assignRole('partner');
        
        // Partner kaydı oluştur
        $partner = Partner::firstOrCreate([
            'user_id' => $user->id
        ], [
            'company_name' => 'Akdeniz Turizm A.Ş.',
            'tax_number' => '1234567890',
            'tax_office' => 'Antalya Vergi Dairesi',
            'phone' => '0242 123 45 67',
            'address' => 'Lara Caddesi No:123 Muratpaşa',
            'city' => 'Antalya',
            'country' => 'TR',
            'postal_code' => '07100',
            'website' => 'www.akdenizturizm.com',
            'contact_person' => 'Mehmet Yılmaz',
            'contact_email' => 'mehmet@akdenizturizm.com',
            'contact_phone' => '0532 123 45 67',
            'status' => 'active',
            'default_commission_rate' => 15.00,
            'contract_start_date' => now(),
            'contract_end_date' => now()->addYear(),
            'tourism_certificate_number' => 'TB-2024-001234',
            'tourism_certificate_valid_until' => now()->addYears(2),
            'onboarding_completed' => true,
            'onboarding_completed_at' => now(),
            'agreement_accepted' => true,
            'agreement_accepted_at' => now(),
            'notes' => 'Test partner hesabı',
        ]);
        
        $this->command->info('Partner seeder completed!');
        $this->command->info('Email: partner@example.com');
        $this->command->info('Password: password');
        
        // İkinci bir partner daha oluşturalım (onboarding tamamlanmamış)
        $user2 = User::firstOrCreate([
            'email' => 'newpartner@example.com'
        ], [
            'name' => 'New Partner',
            'password' => Hash::make('password'),
        ]);
        
        $user2->assignRole('partner');
        
        Partner::firstOrCreate([
            'user_id' => $user2->id
        ], [
            'company_name' => 'Ege Otelcilik Ltd. Şti.',
            'tax_number' => '0987654321',
            'tax_office' => 'İzmir Vergi Dairesi',
            'status' => 'pending',
            'default_commission_rate' => 15.00,
            'onboarding_completed' => false,
        ]);
        
        $this->command->info('New partner (not onboarded) created!');
        $this->command->info('Email: newpartner@example.com');
        $this->command->info('Password: password');
    }
}