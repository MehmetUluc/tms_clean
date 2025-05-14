<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Plugins\Vendor\Models\Vendor;
use Illuminate\Support\Facades\Hash;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create Vendor role if it doesn't exist
$vendorRole = Role::firstOrCreate(['name' => 'vendor']);

// Create a new user
$user = User::create([
    'name' => 'Vendor User',
    'email' => 'vendor@example.com',
    'password' => Hash::make('password'),
]);

// Assign the vendor role
$user->assignRole($vendorRole);

// Create a vendor record
$vendor = Vendor::create([
    'user_id' => $user->id,
    'company_name' => 'Example Vendor Company',
    'tax_number' => '1234567890',
    'tax_office' => 'Example Tax Office',
    'phone' => '+90 555 123 4567',
    'address' => 'Example Address 123',
    'city' => 'Istanbul',
    'country' => 'Turkey',
    'postal_code' => '34000',
    'website' => 'https://example.com',
    'contact_person' => 'Vendor Contact Person',
    'contact_email' => 'contact@example.com',
    'contact_phone' => '+90 555 987 6543',
    'status' => 'active',
    'default_commission_rate' => 10.00,
    'contract_start_date' => now(),
    'contract_end_date' => now()->addYear(),
    'notes' => 'Example vendor created by script',
]);

echo "Vendor kullanıcısı ve kaydı başarıyla oluşturuldu.\n";
echo "Kullanıcı bilgileri:\n";
echo "Email: vendor@example.com\n";
echo "Şifre: password\n";
echo "Vendor ID: {$vendor->id}\n";
echo "Vendor Company: {$vendor->company_name}\n";