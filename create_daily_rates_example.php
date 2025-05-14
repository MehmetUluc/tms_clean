<?php
/**
 * Example script showing how to use the simplified DailyRateService
 * 
 * This script demonstrates importing bulk pricing data from an external system
 * and efficiently saving it using the DailyRateService
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Check for command line arguments
if (!isset($argv[1])) {
    echo "Usage: php create_daily_rates_example.php <rate_plan_id>\n";
    exit(1);
}

$ratePlanId = (int) $argv[1];

// Get the service
$dailyRateService = app(\App\Plugins\Pricing\Services\DailyRateService::class);

// Simulate data from external system (e.g., OTA or channel manager)
$externalData = [];

// Generate sample data for the next 90 days
$startDate = \Carbon\Carbon::now();
$basePrice = 100.00;

echo "Generating sample data for rate plan #{$ratePlanId}...\n";

// Create sample price data
for ($day = 0; $day < 90; $day++) {
    $date = $startDate->copy()->addDays($day);
    $dateString = $date->format('Y-m-d');
    
    // Weekends are more expensive
    $priceMultiplier = 1.0;
    if ($date->isWeekend()) {
        $priceMultiplier = 1.3;
    }
    
    // Add some random variation
    $randomFactor = mt_rand(90, 110) / 100;
    
    // Calculate price
    $price = round($basePrice * $priceMultiplier * $randomFactor, 2);
    
    // Add to external data array
    $externalData[$dateString] = [
        'base_price' => $price,
        'currency' => 'TRY',
        'is_closed' => false,
        'min_stay_arrival' => 1,
        'status' => 'available',
        'notes' => 'Imported from example script',
    ];
}

echo "Generated prices for " . count($externalData) . " days\n";

// BENCHMARK: Compare different methods

// Method 1: One by one using updateOrCreate (very slow for many records)
$startTime = microtime(true);

echo "Method 1: Saving prices one by one...\n";

$count = 0;
// We'll only do a small batch for demonstration
$sampleDates = array_slice($externalData, 0, 10, true);

foreach ($sampleDates as $date => $data) {
    $dailyRateService->saveDailyRate($ratePlanId, $date, $data);
    $count++;
}

$endTime = microtime(true);
$duration = round($endTime - $startTime, 4);
echo "Saved {$count} records one by one in {$duration} seconds\n";

// Method 2: Using bulk save from array (efficient)
$startTime = microtime(true);

echo "Method 2: Saving prices using bulk save from array...\n";

$result = $dailyRateService->saveDailyRatesFromArray($ratePlanId, $externalData);

$endTime = microtime(true);
$duration = round($endTime - $startTime, 4);
echo "Saved " . count($externalData) . " records in bulk in {$duration} seconds\n";

// Method 3: Using bulk save with date range (efficient for uniform pricing)
$startTime = microtime(true);

echo "Method 3: Saving uniform prices for a date range...\n";

$bulkData = [
    'base_price' => 150.00,
    'currency' => 'TRY',
    'is_closed' => false,
    'min_stay_arrival' => 2,
    'status' => 'available',
    'notes' => 'Bulk update example',
];

$result = $dailyRateService->bulkSaveDailyRates(
    $ratePlanId, 
    $startDate->copy()->addDays(100)->format('Y-m-d'), 
    $startDate->copy()->addDays(130)->format('Y-m-d'), 
    $bulkData
);

$endTime = microtime(true);
$duration = round($endTime - $startTime, 4);
echo "Saved 31 records with uniform price in {$duration} seconds\n";

echo "\nDone! You can now verify the pricing data in the database.\n";