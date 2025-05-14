<?php

// MySQL raw query to fix the JSON columns in hotels table
// This script directly executes SQL commands to fix the issue

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Update the column definitions to use proper JSON defaults
DB::statement("ALTER TABLE hotels MODIFY amenities JSON NULL DEFAULT (JSON_ARRAY())");
DB::statement("ALTER TABLE hotels MODIFY policies JSON NULL DEFAULT (JSON_ARRAY())");
DB::statement("ALTER TABLE hotels MODIFY gallery JSON NULL DEFAULT (JSON_ARRAY())");
DB::statement("ALTER TABLE hotels MODIFY check_in_out JSON NULL DEFAULT (JSON_OBJECT('check_in_from', '14:00', 'check_in_until', '23:59', 'check_out_from', '07:00', 'check_out_until', '12:00'))");

echo "JSON columns in hotels table have been fixed.\n";