<?php

namespace Tests\Feature\PricingV2;

use Tests\TestCase;
use App\Models\User;
use App\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Models\BoardType;
use App\Plugins\PricingV2\Models\RatePlanV2;
use App\Plugins\PricingV2\Models\RatePeriodV2;
use App\Plugins\PricingV2\Models\RateExceptionV2;
use App\Plugins\PricingV2\Services\PricingV2Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PricingV2PluginTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $hotel;
    protected $room;
    protected $boardType;
    protected $ratePlan;
    protected $ratePeriod;
    protected $pricingService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Create test hotel
        $this->hotel = Hotel::factory()->create([
            'name' => 'Test Hotel',
        ]);

        // Create test room
        $this->room = Room::factory()->create([
            'hotel_id' => $this->hotel->id,
            'name' => 'Test Room',
        ]);

        // Create board type
        $this->boardType = BoardType::factory()->create([
            'name' => 'All Inclusive',
        ]);

        // Create rate plan
        $this->ratePlan = RatePlanV2::create([
            'name' => 'Test Rate Plan',
            'hotel_id' => $this->hotel->id,
            'is_active' => true,
            'minimum_stay' => 1,
            'is_refundable' => true,
            'applies_to_all_rooms' => true,
        ]);

        // Create rate period
        $this->ratePeriod = RatePeriodV2::create([
            'rate_plan_id' => $this->ratePlan->id,
            'name' => 'Test Period',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(30),
            'base_price' => 100.00,
            'min_stay' => 1,
            'status' => true,
        ]);

        // Get an instance of the pricing service
        $this->pricingService = app(PricingV2Service::class);
    }

    /** @test */
    public function it_can_create_a_rate_plan()
    {
        $response = $this->actingAs($this->user)->post(route('filament.admin.resources.pricing-v2.rate-plans.create'), [
            'name' => 'New Rate Plan',
            'hotel_id' => $this->hotel->id,
            'is_active' => true,
            'minimum_stay' => 2,
            'is_refundable' => true,
        ]);

        $this->assertDatabaseHas('rate_plans_v2', [
            'name' => 'New Rate Plan',
            'hotel_id' => $this->hotel->id,
        ]);
    }

    /** @test */
    public function it_can_calculate_price_for_a_date()
    {
        $date = Carbon::now()->addDays(5);
        $price = $this->pricingService->getPriceForDate($this->ratePlan->id, $date, 2, 0);

        $this->assertEquals(100.00, $price);
    }

    /** @test */
    public function it_can_handle_date_exceptions()
    {
        $date = Carbon::now()->addDays(10);
        
        // Create an exception for the date
        RateExceptionV2::create([
            'rate_period_id' => $this->ratePeriod->id,
            'name' => 'Holiday Exception',
            'date' => $date->format('Y-m-d'),
            'base_price' => 150.00,
            'status' => true,
        ]);

        $price = $this->pricingService->getPriceForDate($this->ratePlan->id, $date, 2, 0);

        $this->assertEquals(150.00, $price);
    }

    /** @test */
    public function it_can_calculate_total_price_for_a_stay()
    {
        $startDate = Carbon::now()->addDays(5);
        $endDate = Carbon::now()->addDays(7);
        
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        $priceData = $this->pricingService->calculateFinalPrice($this->ratePlan, $dates, 2, 0);

        $this->assertTrue($priceData['available']);
        $this->assertEquals(300.00, $priceData['total_price']); // 3 nights * 100.00
    }

    /** @test */
    public function it_can_close_dates_with_exceptions()
    {
        $date = Carbon::now()->addDays(15);
        
        // Create a closed exception for the date
        RateExceptionV2::create([
            'rate_period_id' => $this->ratePeriod->id,
            'name' => 'Closed Date',
            'date' => $date->format('Y-m-d'),
            'is_closed' => true,
            'status' => true,
        ]);

        $price = $this->pricingService->getPriceForDate($this->ratePlan->id, $date, 2, 0);

        $this->assertNull($price); // Price should be null when date is closed
    }
}