<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Pricing\Models\RatePlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// We'll create test data directly in the DB
echo "Creating test inventory data...\n";

// First get all active hotels
$hotels = Hotel::where('is_active', true)->get();
echo "Found " . $hotels->count() . " active hotels.\n";

// Date range for next 30 days
$startDate = Carbon::today();
$endDate = $startDate->copy()->addDays(30);
$dateRange = [];

$current = $startDate->copy();
while ($current->lt($endDate)) {
    $dateRange[] = $current->format('Y-m-d');
    $current->addDay();
}

echo "Creating inventory for " . count($dateRange) . " days.\n";

// Process each hotel
foreach ($hotels as $hotel) {
    echo "Processing hotel: " . $hotel->name . "\n";
    
    // Get room types for this hotel
    $rooms = Room::where('hotel_id', $hotel->id)->get();
    $roomTypeIds = $rooms->pluck('room_type_id')->unique()->toArray();
    
    echo "  Found " . count($roomTypeIds) . " room types.\n";
    
    // Get rate plans for these room types
    $ratePlans = RatePlan::whereIn('room_type_id', $roomTypeIds)->get();
    
    if ($ratePlans->isEmpty()) {
        echo "  No rate plans found, creating default rate plans...\n";
        
        // Create a default rate plan for each room type
        foreach ($roomTypeIds as $roomTypeId) {
            $roomType = RoomType::find($roomTypeId);
            $basePrice = rand(800, 3000); // Random price between 800-3000
            
            $ratePlan = new RatePlan();
            $ratePlan->name = "Standard Rate Plan";
            $ratePlan->room_type_id = $roomTypeId;
            $ratePlan->base_price = $basePrice;
            $ratePlan->is_active = true;
            $ratePlan->save();
            
            echo "    Created rate plan for " . $roomType->name . " with price " . $basePrice . "\n";
        }
        
        // Refresh rate plans
        $ratePlans = RatePlan::whereIn('room_type_id', $roomTypeIds)->get();
    }
    
    echo "  Creating inventory records for " . $ratePlans->count() . " rate plans.\n";
    
    // For each rate plan and date, create inventory
    foreach ($ratePlans as $ratePlan) {
        $roomsForType = $rooms->where('room_type_id', $ratePlan->room_type_id);
        
        if ($roomsForType->isEmpty()) {
            echo "    No rooms found for rate plan " . $ratePlan->name . "\n";
            continue;
        }
        
        // Pick a room to associate with this rate plan
        $room = $roomsForType->first();
        
        echo "    Creating inventory for rate plan " . $ratePlan->name . " and room " . $room->name . "\n";
        
        // Create inventory for each date
        foreach ($dateRange as $date) {
            DB::table('inventories')->updateOrInsert(
                [
                    'rate_plan_id' => $ratePlan->id,
                    'room_id' => $room->id,
                    'date' => $date,
                ],
                [
                    'available' => rand(1, 10), // Random availability
                    'total' => 10,
                    'is_closed' => false,
                    'stop_sell' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}

echo "Done! Inventory created successfully.\n";