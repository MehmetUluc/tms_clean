<?php

// This script is designed to be run from the command line
// php assign_superadmin.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Import required classes
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

try {
    
    // Check if user with ID 1 exists
    $user = DB::table('users')->where('id', 1)->first();
    
    if (!$user) {
        echo "Creating admin user with ID=1...\n";
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123456'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        echo "Admin user created with email: admin@example.com and password: admin123456\n";
    } else {
        echo "User with ID 1 already exists.\n";
    }
    
    // Check if super_admin role exists
    $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
    
    if (!$superAdminRole) {
        echo "Creating super_admin role...\n";
        DB::table('roles')->insert([
            'name' => 'super_admin',
            'guard_name' => 'web',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        echo "Super admin role created.\n";
    } else {
        echo "Super admin role already exists.\n";
    }
    
    // Check if user is already assigned to super_admin role
    $hasRole = DB::table('model_has_roles')
                ->where('role_id', $superAdminRole->id)
                ->where('model_id', 1)
                ->where('model_type', 'App\\Models\\User')
                ->exists();
    
    if (!$hasRole) {
        echo "Assigning super_admin role to user ID 1...\n";
        DB::table('model_has_roles')->insert([
            'role_id' => $superAdminRole->id,
            'model_id' => 1,
            'model_type' => 'App\\Models\\User',
        ]);
        echo "Super admin role assigned to user ID 1.\n";
    } else {
        echo "User ID 1 already has super_admin role.\n";
    }
    
    echo "Setup complete!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

$kernel->terminate(null, 0);