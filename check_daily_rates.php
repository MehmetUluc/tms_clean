<?php

// Bootstrap Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get daily rates
$dailyRates = \App\Plugins\Pricing\Models\DailyRate::with('ratePlan.room')
    ->orderBy('id')
    ->take(5)
    ->get();

echo "=== DAILY RATES ===\n";
foreach ($dailyRates as $rate) {
    echo "ID: {$rate->id}\n";
    echo "Rate Plan: {$rate->rate_plan_id}\n";
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
    
    // Get rate plan and room information if available
    if ($rate->ratePlan && $rate->ratePlan->room) {
        echo "Room: {$rate->ratePlan->room->name}\n";
        echo "Room Pricing Method: {$rate->ratePlan->room->pricing_calculation_method}\n";
    }
    
    echo "-------------------\n";
}