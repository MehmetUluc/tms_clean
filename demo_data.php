<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Pricing\Models\RatePlan;
use Illuminate\Support\Facades\DB;

echo "Creating demo data for BookingWizard testing...\n";

// 1. List all hotels
$hotels = Hotel::where('is_active', true)->get();
echo "Found " . $hotels->count() . " active hotels.\n";

foreach ($hotels as $hotel) {
    echo "Processing hotel: " . $hotel->name . " (ID: " . $hotel->id . ")\n";
    
    // Get room types for this hotel
    $rooms = Room::where('hotel_id', $hotel->id)->get();
    
    echo "  Found " . $rooms->count() . " rooms\n";
    
    // Create a rate plan for each room type if it doesn't exist
    $roomTypeIds = $rooms->pluck('room_type_id')->unique();
    
    foreach ($roomTypeIds as $roomTypeId) {
        $roomType = RoomType::find($roomTypeId);
        if (!$roomType) continue;
        
        echo "  Processing room type: " . $roomType->name . " (ID: " . $roomTypeId . ")\n";
        
        // Check if rate plans exist
        $ratePlans = RatePlan::where('room_type_id', $roomTypeId)->count();
        
        if ($ratePlans == 0) {
            echo "    No rate plans found, creating new ones\n";
            
            // Create standard rate plan
            $standardPlan = new RatePlan();
            $standardPlan->name = "Standard Rate - Breakfast Included";
            $standardPlan->room_type_id = $roomTypeId;
            $standardPlan->base_price = rand(800, 1200);
            $standardPlan->is_active = true;
            $standardPlan->save();
            
            // Create premium rate plan
            $premiumPlan = new RatePlan();
            $premiumPlan->name = "Premium Rate - All Inclusive";
            $premiumPlan->room_type_id = $roomTypeId;
            $premiumPlan->base_price = rand(1500, 2500);
            $premiumPlan->is_active = true;
            $premiumPlan->save();
            
            echo "    Created 2 new rate plans\n";
        } else {
            echo "    Found " . $ratePlans . " existing rate plans\n";
        }
    }
}

echo "\nDemo data creation complete!\n";