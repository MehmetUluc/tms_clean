<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Daily Rates Table Columns:\n";
$columns = \Schema::getColumnListing('daily_rates');
print_r($columns);