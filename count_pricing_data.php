<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\DailyRate;
use App\Plugins\Pricing\Models\OccupancyRate;
use App\Plugins\Pricing\Models\Inventory;
use App\Plugins\Pricing\Models\ChildPolicy;

echo "Fiyatlandırma Veri İstatistikleri:\n";
echo "================================\n";
echo "Fiyat Planı Sayısı: " . RatePlan::count() . "\n";
echo "Günlük Fiyat Kaydı Sayısı: " . DailyRate::count() . "\n";
echo "Doluluk Fiyatı Kaydı Sayısı: " . OccupancyRate::count() . "\n";
echo "Envanter Kaydı Sayısı: " . Inventory::count() . "\n";
echo "Çocuk Politikası Sayısı: " . ChildPolicy::count() . "\n";

// Show sample rate plan details
$ratePlan = RatePlan::first();
if ($ratePlan) {
    echo "\nÖrnek Fiyat Planı Detayları:\n";
    echo "=========================\n";
    echo "Ad: " . $ratePlan->name . "\n";
    echo "Açıklama: " . $ratePlan->description . "\n";
    echo "Yemek Planı: " . $ratePlan->meal_plan . "\n";
    echo "Doluluk Bazlı Fiyat: " . ($ratePlan->occupancy_pricing ? 'Evet' : 'Hayır') . "\n";
    
    // Show daily rates for this plan
    $dailyRates = $ratePlan->dailyRates()->take(5)->get();
    echo "\nÖrnek Günlük Fiyatlar:\n";
    foreach ($dailyRates as $rate) {
        echo $rate->date->format('Y-m-d') . ": " . $rate->base_price . " " . $rate->currency . "\n";
    }
    
    // Show occupancy rates if applicable
    if ($ratePlan->occupancy_pricing) {
        $occupancyRates = $ratePlan->occupancyRates()->take(5)->get();
        echo "\nÖrnek Doluluk Bazlı Fiyatlar:\n";
        foreach ($occupancyRates as $rate) {
            $dateStr = $rate->date ? $rate->date->format('Y-m-d') : 'Varsayılan';
            echo "Kişi: " . $rate->occupancy . ", Tarih: " . $dateStr . ", Fiyat: " . $rate->price . " " . $rate->currency . "\n";
        }
    }
    
    // Show child policies
    $childPolicies = $ratePlan->childPolicies()->get();
    echo "\nÇocuk Politikaları:\n";
    foreach ($childPolicies as $policy) {
        echo "Yaş: " . $policy->min_age . "-" . $policy->max_age . ", Politika: " . $policy->policy_type . ", Miktar: " . $policy->amount . "\n";
    }
}