<?php

// This script is designed to be run from the command line
// php setup_permissions.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run permission migration
echo "Running permission migration...\n";
$exitCode = $kernel->call('migrate', [
    '--path' => 'database/migrations/2025_05_04_000000_create_permission_tables.php',
    '--force' => true,
]);

if ($exitCode > 0) {
    echo "Error running permissions migration.\n";
    exit(1);
}

// Seed super admin
echo "Seeding super admin role...\n";
$kernel->call('db:seed', [
    '--class' => 'Database\\Seeders\\SuperAdminSeeder',
    '--force' => true,
]);

// Check if user 1 exists, if not create it
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

if (!User::find(1)) {
    echo "Creating admin user with ID=1...\n";
    $user = User::create([
        'id' => 1,
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('admin123456'),
    ]);
    
    // Get or create super_admin role
    $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
    
    // Assign role to user
    $user->assignRole($superAdminRole);
    
    echo "Admin user created with email: admin@example.com and password: admin123456\n";
}

echo "Setup complete!\n";

$kernel->terminate(null, $exitCode);