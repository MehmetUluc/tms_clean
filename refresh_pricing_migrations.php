<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Drop our tables first to ensure clean migration
if (Schema::hasTable('inventories')) {
    Schema::dropIfExists('inventories');
}

if (Schema::hasTable('child_policies')) {
    Schema::dropIfExists('child_policies');
}

if (Schema::hasTable('occupancy_rates')) {
    Schema::dropIfExists('occupancy_rates');
}

if (Schema::hasTable('daily_rates')) {
    Schema::dropIfExists('daily_rates');
}

if (Schema::hasTable('rate_plans')) {
    Schema::dropIfExists('rate_plans');
}

echo "Dropped pricing tables if they existed.\n";

// Get all migration files in our plugin's migrations directory
$migrationPath = __DIR__ . '/app/Plugins/Pricing/database/migrations';
echo "Looking for migrations in: $migrationPath\n";

$files = scandir($migrationPath);
echo "Found files: " . implode(", ", $files) . "\n";

// Filter only PHP files
$migrationFiles = array_filter($files, function($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'php';
});

if (empty($migrationFiles)) {
    echo "No migration files found!\n";
    exit(1);
}

echo "Running pricing migrations...\n";

foreach ($migrationFiles as $file) {
    echo "Running migration: $file\n";
    
    // Include the migration file
    $migration = include "$migrationPath/$file";
    
    // Run the migration
    $migration->up();
    
    echo "Completed migration: $file\n";
}

echo "Migration completed successfully.\n";