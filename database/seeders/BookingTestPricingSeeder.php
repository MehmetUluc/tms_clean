<?php

namespace Database\Seeders;

use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\DailyRate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingTestPricingSeeder extends Seeder
{
    public function run()
    {
        $startDate = Carbon::parse('2025-05-23');
        $endDate = Carbon::parse('2025-06-15');
        
        // Get all hotels
        $hotels = Hotel::with('rooms')->get();
        
        if ($hotels->isEmpty()) {
            $this->command->error('No hotels found! Please run BookingTestDataSeeder first.');
            return;
        }
        
        // Get board types
        $boardTypes = BoardType::all()->keyBy('code');
        
        foreach ($hotels as $hotel) {
            $this->command->info("Creating pricing for hotel: {$hotel->name}");
            
            foreach ($hotel->rooms as $room) {
                // Create rate plans for each board type
                foreach ($boardTypes as $boardType) {
                    // Skip some board types for certain room types for realism
                    if ($room->room_type_id == 1 && in_array($boardType->code, ['UAI'])) {
                        continue; // Standard rooms don't have Ultra All Inclusive
                    }
                    
                    // Check if rate plan already exists
                    // Make some rooms per-person pricing for variety
                    $isPerPerson = in_array($room->room_type_id, [1, 3]) || $boardType->code === 'AI' || $boardType->code === 'UAI';
                    
                    $ratePlan = RatePlan::firstOrCreate(
                        [
                            'hotel_id' => $hotel->id,
                            'room_id' => $room->id,
                            'board_type_id' => $boardType->id,
                        ],
                        [
                            'is_per_person' => $isPerPerson,
                            'status' => true,
                        ]
                    );
                    
                    // Create daily rates
                    $this->createDailyRates($ratePlan, $startDate, $endDate, $hotel, $room, $boardType);
                }
            }
        }
        
        $this->command->info('Pricing data created successfully!');
    }
    
    private function createDailyRates($ratePlan, $startDate, $endDate, $hotel, $room, $boardType)
    {
        $currentDate = $startDate->copy();
        
        // Base prices based on hotel star rating
        $starMultiplier = [
            3 => 1.0,
            4 => 1.5,
            5 => 2.5
        ][$hotel->star_rating] ?? 1.0;
        
        // Room type multipliers
        $roomMultiplier = [
            1 => 1.0,    // Standard
            2 => 1.2,    // Deluxe
            3 => 1.5,    // Suite
            4 => 1.3,    // Family
            5 => 2.0,    // Presidential
        ][$room->room_type_id] ?? 1.0;
        
        // Board type price additions (per person)
        $boardPrices = [
            'RO' => 0,
            'BB' => 15,
            'HB' => 35,
            'FB' => 55,
            'AI' => 75,
            'UAI' => 100,
        ];
        
        $boardAddition = $boardPrices[$boardType->code] ?? 0;
        
        // Base price calculation
        $baseRoomPrice = 50 * $starMultiplier * $roomMultiplier;
        
        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $isWeekend = in_array($dayOfWeek, [5, 6]); // Friday, Saturday
            $isSummer = $currentDate->month == 6; // June is high season
            
            // Weekend multiplier
            $weekendMultiplier = $isWeekend ? 1.3 : 1.0;
            
            // Seasonal multiplier
            $seasonalMultiplier = $isSummer ? 1.4 : 1.0;
            
            // Special date multipliers (holidays, events)
            $specialMultiplier = 1.0;
            if ($currentDate->day == 1 && $currentDate->month == 6) {
                $specialMultiplier = 1.5; // June 1st - Children's Day in Turkey
            }
            
            // Calculate final price
            if ($ratePlan->is_per_person) {
                // Per person pricing
                $adultPrice = ($baseRoomPrice / 2) * $weekendMultiplier * $seasonalMultiplier * $specialMultiplier;
                $childPrice = $adultPrice * 0.5; // Children 50% off
                
                // Add board price
                $adultPrice += $boardAddition;
                $childPrice += ($boardAddition * 0.7); // Children get 30% off board
                
                // Add some randomness (±10%)
                $adultPrice *= (0.9 + (rand(0, 20) / 100));
                $childPrice *= (0.9 + (rand(0, 20) / 100));
                
                $basePrice = $adultPrice;
                $extraPersonPrice = 0; // Not used in per-person pricing
                
                // Create prices_json for different occupancy levels
                $maxOccupancy = $room->capacity_adults ?: 3;
                $pricesJson = [];
                for ($i = 1; $i <= $maxOccupancy; $i++) {
                    if ($i == 1) {
                        // Single occupancy - slightly higher price
                        $pricesJson[$i] = round($adultPrice * 1.2, 2);
                    } elseif ($i == 2) {
                        // Double occupancy - standard price
                        $pricesJson[$i] = round($adultPrice, 2);
                    } else {
                        // Triple+ occupancy - slightly discounted
                        $pricesJson[$i] = round($adultPrice * (1 - (($i - 2) * 0.05)), 2);
                    }
                }
            } else {
                // Per unit pricing
                $unitPrice = $baseRoomPrice * $weekendMultiplier * $seasonalMultiplier * $specialMultiplier;
                
                // Add board price for typical occupancy
                $unitPrice += ($boardAddition * 2); // Assume 2 people
                
                // Add some randomness (±10%)
                $unitPrice *= (0.9 + (rand(0, 20) / 100));
                
                $basePrice = $unitPrice;
                $extraPersonPrice = $boardAddition * 0.8; // Extra person pays 80% of board price
                $pricesJson = null; // Not used for unit pricing
            }
            
            // Inventory (more rooms available on weekdays)
            $inventory = $isWeekend ? rand(1, 3) : rand(3, 8);
            
            // Stop sale occasionally (5% chance)
            $stopSale = rand(1, 100) <= 5;
            
            // Check if daily rate already exists for this date
            DailyRate::updateOrCreate(
                [
                    'rate_plan_id' => $ratePlan->id,
                    'date' => $currentDate->format('Y-m-d'),
                ],
                [
                    'base_price' => round($basePrice, 2),
                    'currency' => 'TRY',
                    'is_closed' => $stopSale,
                    'min_stay_arrival' => $isWeekend ? 2 : 1,
                    'inventory' => $inventory,
                    'status' => $stopSale ? 'sold_out' : ($inventory <= 2 ? 'limited' : 'available'),
                    'is_per_person' => $ratePlan->is_per_person,
                    'prices_json' => $pricesJson,
                    'is_refundable' => true,
                    'sales_type' => 'direct',
                ]
            );
            
            $currentDate->addDay();
        }
    }
}