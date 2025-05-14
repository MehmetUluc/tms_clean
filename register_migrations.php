<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$migrations = [
    '2025_05_04_100000_create_rate_plans_table',
    '2025_05_04_100001_create_daily_rates_table',
    '2025_05_04_100002_create_occupancy_rates_table',
    '2025_05_04_100003_create_child_policies_table',
    '2025_05_04_100004_create_inventories_table',
];

// Get the current batch number
$batch = DB::table('migrations')->max('batch') + 1;

echo "Registering migrations with batch $batch...\n";

foreach ($migrations as $migration) {
    // Check if migration already exists in the database
    $exists = DB::table('migrations')->where('migration', $migration)->exists();
    
    if (!$exists) {
        // Add migration to the migrations table
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch,
        ]);
        echo "Registered migration: $migration\n";
    } else {
        echo "Migration already registered: $migration\n";
    }
}

echo "Registration completed successfully.\n";