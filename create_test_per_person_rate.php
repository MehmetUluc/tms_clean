<?php

// Bootstrap Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find a rate plan that uses per_person pricing method
// If not found, update an existing one to use per_person
$ratePlan = \App\Plugins\Pricing\Models\RatePlan::with('room')->first();

if (!$ratePlan) {
    echo "No rate plan found!\n";
    exit(1);
}

// Update the room to use per_person pricing method
$room = $ratePlan->room;
if (!$room) {
    echo "Rate plan has no room!\n";
    exit(1);
}

$room->pricing_calculation_method = 'per_person';
$room->save();

echo "Updated room #{$room->id} to use per_person pricing method\n";

// Create a test daily rate for this rate plan
$date = new \Carbon\Carbon('2025-05-20'); // Use a future date
$basePrice = 150.00;

// Calculate prices for different occupancy
$prices = [];
for ($i = 1; $i <= 3; $i++) {
    if ($i === 1) {
        $prices[(string)$i] = $basePrice;
    } else {
        // Additional persons at 80% of base price
        $prices[(string)$i] = round($basePrice * 0.8 * $i, 2);
    }
}

// Create or update the daily rate
$dailyRate = \App\Plugins\Pricing\Models\DailyRate::updateOrCreate([
    'rate_plan_id' => $ratePlan->id,
    'date' => $date,
], [
    'base_price' => $basePrice,
    'currency' => 'TRY',
    'is_closed' => false,
    'min_stay_arrival' => 1,
    'status' => 'available',
    'is_per_person' => true,
    'prices_json' => $prices,
    'is_refundable' => true,
]);

echo "Created/updated daily rate #{$dailyRate->id} with per_person pricing\n";

// Create a non-refundable rate for the next day
$date = $date->copy()->addDay();
$nonRefundableRate = \App\Plugins\Pricing\Models\DailyRate::updateOrCreate([
    'rate_plan_id' => $ratePlan->id,
    'date' => $date,
], [
    'base_price' => $basePrice * 0.9, // 10% discount for non-refundable
    'currency' => 'TRY',
    'is_closed' => false,
    'min_stay_arrival' => 1,
    'status' => 'available',
    'is_per_person' => true,
    'prices_json' => [
        '1' => round($basePrice * 0.9, 2),
        '2' => round($basePrice * 0.9 * 0.8 * 2, 2),
        '3' => round($basePrice * 0.9 * 0.8 * 3, 2),
    ],
    'is_refundable' => false,
]);

echo "Created/updated daily rate #{$nonRefundableRate->id} with non-refundable pricing\n";

// Now read all rates for this rate plan to verify
$rates = \App\Plugins\Pricing\Models\DailyRate::where('rate_plan_id', $ratePlan->id)
    ->orderBy('date')
    ->get();

echo "\n=== DAILY RATES FOR RATE PLAN #{$ratePlan->id} ===\n";
foreach ($rates as $rate) {
    echo "ID: {$rate->id}\n";
    echo "Date: {$rate->date->format('Y-m-d')}\n";
    echo "Base Price: {$rate->base_price}\n";
    echo "Is Per Person: " . ($rate->is_per_person ? 'Yes' : 'No') . "\n";
    echo "Is Refundable: " . ($rate->is_refundable ? 'Yes' : 'No') . "\n";
    echo "Pricing Method: " . $rate->getPricingTypeLabel() . "\n";
    
    // Show prices for different occupancy
    echo "Prices:\n";
    $prices = $rate->getAllPrices();
    foreach ($prices as $occupancy => $price) {
        echo "  {$occupancy}: {$price}\n";
    }
    
    echo "-------------------\n";
}