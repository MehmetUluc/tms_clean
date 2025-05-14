<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Models\RoomType;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\DailyRate;
use App\Plugins\Pricing\Models\OccupancyRate;
use App\Plugins\Pricing\Models\Inventory;
use App\Plugins\Pricing\Models\ChildPolicy;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Fiyat bilgileri oluşturuluyor...');

        // Get existing hotels
        $hotels = Hotel::all();
        if ($hotels->isEmpty()) {
            $this->command->error('Fiyatlandırma için otel bulunamadı. Önce otelleri oluşturun.');
            return;
        }

        // Get today's date for reference
        $today = Carbon::today();
        
        // Create pricing for the next 90 days
        $startDate = $today;
        $endDate = $today->copy()->addDays(90);
        
        // Get date periods for rate generation
        $datePeriod = CarbonPeriod::create($startDate, $endDate);
        
        // Meal plan options
        $mealPlans = ['none', 'breakfast', 'half_board', 'full_board', 'all_inclusive'];
        
        foreach ($hotels as $hotel) {
            $this->command->info("'{$hotel->name}' oteli için fiyat planları oluşturuluyor...");
            
            // Get rooms and room types for this hotel
            $rooms = Room::where('hotel_id', $hotel->id)->get();
            $roomTypes = RoomType::whereIn('id', $rooms->pluck('room_type_id')->unique())->get();
            
            if ($rooms->isEmpty()) {
                $this->command->warn("'{$hotel->name}' oteli için oda bulunamadı, fiyat oluşturulamıyor.");
                continue;
            }
            
            foreach ($roomTypes as $roomType) {
                $this->command->info("'{$roomType->name}' oda tipi için fiyat planları oluşturuluyor...");
                
                // Create 2-3 rate plans for each room type
                $numRatePlans = rand(2, 3);
                
                for ($i = 0; $i < $numRatePlans; $i++) {
                    // Determine if this plan is default
                    $isDefault = ($i === 0);
                    
                    // Randomly select meal plan
                    $mealPlan = $mealPlans[array_rand($mealPlans)];
                    
                    // Create rate plan
                    $ratePlan = RatePlan::create([
                        'name' => $this->getRatePlanName($i, $mealPlan),
                        'hotel_id' => $hotel->id,
                        'room_type_id' => $roomType->id,
                        'description' => $this->getRatePlanDescription($mealPlan),
                        'is_active' => true,
                        'is_default' => $isDefault,
                        'occupancy_pricing' => rand(0, 1) === 1, // 50% chance of occupancy pricing
                        'min_stay' => rand(1, 3),
                        'max_stay' => rand(14, 21),
                        'payment_type' => $this->getRandomPaymentType(),
                        'restriction_days' => $this->getRandomRestrictionDays(),
                        'cancellation_policy' => $this->getRandomCancellationPolicy(),
                        'sort_order' => $i,
                        'meal_plan' => $mealPlan,
                    ]);
                    
                    $this->command->info("'{$ratePlan->name}' fiyat planı oluşturuldu.");
                    
                    // Generate rates for this plan
                    if ($ratePlan->occupancy_pricing) {
                        $this->generateOccupancyRates($ratePlan, $datePeriod, $roomType);
                    } else {
                        $this->generateDailyRates($ratePlan, $datePeriod, $roomType);
                    }
                    
                    // Generate child policies
                    $this->generateChildPolicies($ratePlan);
                    
                    // Generate inventory for this rate plan (for each room of this type)
                    $typeRooms = $rooms->where('room_type_id', $roomType->id);
                    foreach ($typeRooms as $room) {
                        $this->generateInventory($ratePlan, $room, $datePeriod);
                    }
                }
            }
        }
        
        $this->command->info('Fiyat bilgileri başarıyla oluşturuldu!');
    }
    
    /**
     * Generate a rate plan name based on index and meal plan
     */
    private function getRatePlanName(int $index, string $mealPlan): string
    {
        $prefixes = ['Ekonomik', 'Standart', 'Ekonomik', 'Esnek', 'Premium', 'Deluxe', 'VIP'];
        $prefix = $prefixes[array_rand($prefixes)];
        
        $mealPlanNames = [
            'none' => 'Sadece Oda',
            'breakfast' => 'Kahvaltı Dahil',
            'half_board' => 'Yarım Pansiyon',
            'full_board' => 'Tam Pansiyon',
            'all_inclusive' => 'Her Şey Dahil'
        ];
        
        return $prefix . ' Plan - ' . $mealPlanNames[$mealPlan];
    }
    
    /**
     * Generate a rate plan description based on meal plan
     */
    private function getRatePlanDescription(string $mealPlan): string
    {
        $descriptions = [
            'none' => 'Sadece konaklama içerir, yemek hizmetleri dahil değildir.',
            'breakfast' => 'Açık büfe kahvaltı dahildir. Diğer öğünler ekstra ücretlidir.',
            'half_board' => 'Kahvaltı ve akşam yemeği dahildir. Öğle yemeği ekstra ücretlidir.',
            'full_board' => 'Kahvaltı, öğle ve akşam yemeği dahildir. İçecekler ekstra ücretlidir.',
            'all_inclusive' => 'Tüm öğünler ve yerli içecekler dahildir. Premium içecekler ekstra ücretlidir.'
        ];
        
        return $descriptions[$mealPlan];
    }
    
    /**
     * Get a random payment type
     */
    private function getRandomPaymentType(): string
    {
        $types = ['pay_online', 'reserve_only', 'inquire_only'];
        $weights = [70, 25, 5]; // 70% pay_online, 25% reserve_only, 5% inquire_only
        
        return $this->weightedRandom($types, $weights);
    }
    
    /**
     * Get random restriction days
     */
    private function getRandomRestrictionDays(): ?array
    {
        // 30% chance of having restrictions
        if (rand(1, 100) > 30) {
            return null;
        }
        
        $days = range(1, 7); // 1 = Monday, 7 = Sunday
        shuffle($days);
        
        // Restrict 1-2 days randomly
        return array_slice($days, 0, rand(1, 2));
    }
    
    /**
     * Get a random cancellation policy
     */
    private function getRandomCancellationPolicy(): string
    {
        $policies = [
            'Bu rezervasyon yapıldıktan sonra iptal edilemez ve iade yapılmaz.',
            'Check-in tarihinden 24 saat öncesine kadar ücretsiz iptal.',
            'Check-in tarihinden 48 saat öncesine kadar ücretsiz iptal, sonrasında ilk gece ücreti alınır.',
            'Check-in tarihinden 7 gün öncesine kadar ücretsiz iptal, sonrasında toplam tutarın %50\'si alınır.',
            'Check-in tarihinden 14 gün öncesine kadar ücretsiz iptal, sonrasında ödeme iade edilmez.'
        ];
        
        return $policies[array_rand($policies)];
    }
    
    /**
     * Generate occupancy-based rates for a rate plan
     */
    private function generateOccupancyRates(RatePlan $ratePlan, CarbonPeriod $datePeriod, RoomType $roomType): void
    {
        // Create default rates for each occupancy (1-4 people)
        $baseRate = $this->getBaseRateForRoomType($roomType);
        
        // Create default rates (no specific date)
        for ($occupancy = 1; $occupancy <= 4; $occupancy++) {
            // Base price increases with occupancy
            $price = $baseRate * $this->getOccupancyMultiplier($occupancy);
            
            OccupancyRate::updateOrCreate(
                [
                    'rate_plan_id' => $ratePlan->id,
                    'occupancy' => $occupancy,
                    'is_default' => true
                ],
                [
                    'date' => null, // Default rate
                    'price' => $price,
                    'currency' => 'TRY'
                ]
            );
        }
        
        // Create date-specific rates with some variations for certain dates
        foreach ($datePeriod as $date) {
            // Skip some dates (only create specific rates for 20% of dates)
            if (rand(1, 100) > 20) {
                continue;
            }
            
            // Weekend and seasonal adjustments
            $dayMultiplier = $this->getDayMultiplier($date);
            
            for ($occupancy = 1; $occupancy <= 4; $occupancy++) {
                $defaultRate = $baseRate * $this->getOccupancyMultiplier($occupancy);
                $adjustedRate = $defaultRate * $dayMultiplier;
                
                // Apply some randomness
                $price = round($adjustedRate * (1 + (rand(-10, 20) / 100)), 2);
                
                OccupancyRate::updateOrCreate(
                    [
                        'rate_plan_id' => $ratePlan->id,
                        'date' => $date,
                        'occupancy' => $occupancy
                    ],
                    [
                        'price' => $price,
                        'currency' => 'TRY',
                        'is_default' => false
                    ]
                );
            }
        }
    }
    
    /**
     * Generate daily rates for a rate plan (unit-based pricing)
     */
    private function generateDailyRates(RatePlan $ratePlan, CarbonPeriod $datePeriod, RoomType $roomType): void
    {
        // Get base rate for this room type (for 2 people)
        $baseRate = $this->getBaseRateForRoomType($roomType);
        
        // Create a daily rate for each date in the period
        foreach ($datePeriod as $date) {
            // Apply day and seasonal adjustments
            $dayMultiplier = $this->getDayMultiplier($date);
            $adjustedRate = $baseRate * $dayMultiplier;
            
            // Apply some randomness (±10% variation)
            $price = round($adjustedRate * (1 + (rand(-10, 10) / 100)), 2);
            
            // Check if this date should be closed
            $isClosed = (rand(1, 100) <= 5); // 5% chance of being closed
            
            DailyRate::updateOrCreate(
                [
                    'rate_plan_id' => $ratePlan->id,
                    'date' => $date
                ],
                [
                    'base_price' => $price,
                    'currency' => 'TRY',
                    'is_closed' => $isClosed,
                    'min_stay_arrival' => rand(1, 3),
                    'status' => $this->getRandomAvailabilityStatus(),
                    'notes' => $isClosed ? 'Bakım çalışmaları nedeniyle kapalı' : null,
                ]
            );
        }
    }
    
    /**
     * Generate inventory records for a rate plan and room
     */
    private function generateInventory(RatePlan $ratePlan, Room $room, CarbonPeriod $datePeriod): void
    {
        // Determine total inventory for this room
        $totalInventory = rand(1, 5);
        
        foreach ($datePeriod as $date) {
            // Randomly calculate available inventory
            $available = $totalInventory;
            
            // Simulate some bookings
            if (rand(0, 100) < 30) { // 30% chance of having some bookings
                $available = max(0, $available - rand(1, $totalInventory));
            }
            
            // Determine if closed for this date (5% chance)
            $isClosed = (rand(1, 100) <= 5);
            
            // Create inventory record
            Inventory::updateOrCreate(
                [
                    'rate_plan_id' => $ratePlan->id,
                    'room_id' => $room->id,
                    'date' => $date
                ],
                [
                    'available' => $isClosed ? 0 : $available,
                    'total' => $totalInventory,
                    'is_closed' => $isClosed,
                    'stop_sell' => (rand(1, 100) <= 3), // 3% chance of stop sell
                    'notes' => $isClosed ? 'Geçici olarak satışa kapalı' : null,
                ]
            );
        }
    }
    
    /**
     * Generate child policies for a rate plan
     */
    private function generateChildPolicies(RatePlan $ratePlan): void
    {
        // Create policy for infants (0-2)
        ChildPolicy::updateOrCreate(
            [
                'rate_plan_id' => $ratePlan->id,
                'min_age' => 0,
                'max_age' => 2,
                'child_number' => 1
            ],
            [
                'policy_type' => 'free',
                'amount' => 0,
                'currency' => 'TRY',
                'max_children' => 1
            ]
        );
        
        // Create policy for children (3-6)
        ChildPolicy::updateOrCreate(
            [
                'rate_plan_id' => $ratePlan->id,
                'min_age' => 3,
                'max_age' => 6,
                'child_number' => 1
            ],
            [
                'policy_type' => 'percentage',
                'amount' => 50, // 50% of adult price
                'currency' => 'TRY',
                'max_children' => 2
            ]
        );
        
        // Create policy for older children (7-12)
        ChildPolicy::updateOrCreate(
            [
                'rate_plan_id' => $ratePlan->id,
                'min_age' => 7,
                'max_age' => 12,
                'child_number' => 1
            ],
            [
                'policy_type' => 'percentage',
                'amount' => 75, // 75% of adult price
                'currency' => 'TRY',
                'max_children' => 2
            ]
        );
        
        // Create fixed-price policy for second child (3-6)
        ChildPolicy::updateOrCreate(
            [
                'rate_plan_id' => $ratePlan->id,
                'min_age' => 3,
                'max_age' => 6,
                'child_number' => 2
            ],
            [
                'policy_type' => 'fixed_price',
                'amount' => rand(100, 200),
                'currency' => 'TRY',
                'max_children' => 2
            ]
        );
    }
    
    /**
     * Get a random availability status
     */
    private function getRandomAvailabilityStatus(): string
    {
        $statuses = ['available', 'limited', 'sold_out'];
        $weights = [80, 15, 5]; // 80% available, 15% limited, 5% sold_out
        
        return $this->weightedRandom($statuses, $weights);
    }
    
    /**
     * Get base rate for a room type
     */
    private function getBaseRateForRoomType(RoomType $roomType): float
    {
        // Base rates by room type category
        $baseRates = [
            'standard' => rand(250, 400),
            'deluxe' => rand(400, 600),
            'suite' => rand(700, 1200),
            'family' => rand(600, 950),
            'apartment' => rand(800, 1500),
            'villa' => rand(1500, 3000),
            'bungalow' => rand(600, 1000),
            'cottage' => rand(500, 900),
        ];
        
        // Get category or default to standard
        $category = strtolower($roomType->category ?? 'standard');
        
        // If category doesn't exist in our map, use standard
        if (!isset($baseRates[$category])) {
            $category = 'standard';
        }
        
        // Apply some randomness to the base rate
        return $baseRates[$category] * (1 + (rand(-10, 10) / 100));
    }
    
    /**
     * Get multiplier based on occupancy
     */
    private function getOccupancyMultiplier(int $occupancy): float
    {
        switch ($occupancy) {
            case 1:
                return 0.8; // 80% of the base rate (for 2)
            case 2:
                return 1.0; // Base rate
            case 3:
                return 1.3; // 130% of the base rate
            case 4:
                return 1.6; // 160% of the base rate
            default:
                return 1.0;
        }
    }
    
    /**
     * Get multiplier based on day of week and season
     */
    private function getDayMultiplier(Carbon $date): float
    {
        $multiplier = 1.0;
        
        // Weekend multiplier (Friday and Saturday)
        $dayOfWeek = $date->dayOfWeek;
        if ($dayOfWeek === Carbon::FRIDAY || $dayOfWeek === Carbon::SATURDAY) {
            $multiplier *= 1.25;
        }
        
        // Summer season multiplier (June-August)
        $month = $date->month;
        if ($month >= 6 && $month <= 8) {
            $multiplier *= 1.3;
        }
        
        // Special holiday multipliers
        // New Year
        if ($month === 1 && $date->day <= 3) {
            $multiplier *= 1.5;
        }
        
        return $multiplier;
    }
    
    /**
     * Weighted random selection
     */
    private function weightedRandom(array $items, array $weights): mixed
    {
        $totalWeight = array_sum($weights);
        $randomWeight = mt_rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($items as $index => $item) {
            $currentWeight += $weights[$index];
            if ($randomWeight <= $currentWeight) {
                return $item;
            }
        }
        
        return $items[0]; // Fallback
    }
}