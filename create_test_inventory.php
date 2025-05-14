<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Pricing\Models\Inventory;
use Carbon\Carbon;

$startDate = Carbon::now()->startOfDay();
$endDate = $startDate->copy()->addDays(90);
$dates = [];

$current = $startDate->copy();
while ($current->lt($endDate)) {
    $dates[] = $current->format('Y-m-d');
    $current->addDay();
}

echo "Generating inventory records for " . count($dates) . " days...\n";

$roomTypes = RoomType::with('rooms')->get();
foreach ($roomTypes as $roomType) {
    echo "Processing room type: " . $roomType->name . "\n";
    
    $rooms = $roomType->rooms;
    if ($rooms->count() == 0) {
        echo "  No rooms found for this room type, skipping.\n";
        continue;
    }
    
    $roomCount = $rooms->count();
    $hotel = $rooms->first()->hotel;
    
    echo "  Creating inventory for {$roomCount} rooms at {$hotel->name}\n";
    
    foreach ($dates as $date) {
        Inventory::updateOrCreate(
            [
                'room_type_id' => $roomType->id,
                'date' => $date,
            ],
            [
                'total_count' => $roomCount,
                'available_count' => $roomCount,
                'hotel_id' => $hotel->id,
            ]
        );
    }
}

echo "Done! Inventory records created successfully.\n";