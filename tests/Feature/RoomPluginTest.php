<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Core\Models\Agency;
use Tms\Hotel\Models\Hotel;
use Tms\Hotel\Models\RoomType;
use Tms\Room\Models\Room;
use Tms\Room\Models\RoomMaintenance;
use Tms\Room\Models\Promotion;
use Tms\Room\Models\RoomPricing;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomPluginTest extends TestCase
{
    // use RefreshDatabase;

    protected $agency;
    protected $hotel;
    protected $roomType;

    public function setUp(): void
    {
        parent::setUp();

        // Temel modelleri oluştur
        $this->agency = Agency::create([
            'name' => 'Test Agency',
            'code' => 'TEST001',
            'email' => 'test@example.com',
            'is_active' => true,
            'slug' => 'test-agency'
        ]);

        $this->hotel = Hotel::create([
            'agency_id' => $this->agency->id,
            'name' => 'Test Hotel',
            'slug' => 'test-hotel',
            'address' => 'Test Address',
            'star_rating' => 5,
            'is_active' => true
        ]);

        $this->roomType = RoomType::create([
            'agency_id' => $this->agency->id,
            'hotel_id' => $this->hotel->id,
            'name' => 'Standard Room',
            'capacity' => 2,
            'max_adults' => 2,
            'max_children' => 1,
            'base_price' => 100.00,
            'is_active' => true
        ]);
    }

    #[Test]
    public function it_can_create_a_room()
    {
        $room = Room::create([
            'agency_id' => $this->agency->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '101',
            'floor' => 1,
            'status' => 'available',
            'is_accessible' => false,
            'is_smoking' => false,
            'is_active' => true,
            'features' => ['tv', 'minibar', 'safe']
        ]);

        $this->assertNotNull($room);
        $this->assertEquals('101', $room->room_number);
        $this->assertEquals('available', $room->status);
        $this->assertTrue(in_array('minibar', $room->features));
    }

    #[Test]
    public function it_can_create_room_maintenance_records()
    {
        $room = Room::create([
            'agency_id' => $this->agency->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '102',
            'floor' => 1,
            'status' => 'available',
            'is_active' => true
        ]);

        $maintenance = RoomMaintenance::create([
            'agency_id' => $this->agency->id,
            'room_id' => $room->id,
            'title' => 'Bathroom Repairs',
            'description' => 'Fix leaking shower',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'scheduled',
            'priority' => 'high',
            'cost' => 250.00
        ]);

        $this->assertNotNull($maintenance);
        $this->assertEquals('Bathroom Repairs', $maintenance->title);
        $this->assertEquals('scheduled', $maintenance->status);
        $this->assertEquals('high', $maintenance->priority);
    }

    #[Test]
    public function it_can_create_promotions()
    {
        $promotion = Promotion::create([
            'agency_id' => $this->agency->id,
            'name' => 'Summer Special',
            'description' => 'Special summer discount',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(3)->format('Y-m-d'),
            'discount_type' => 'percentage',
            'discount_value' => 15.00,
            'minimum_stay' => 3,
            'is_active' => true
        ]);

        $this->assertNotNull($promotion);
        $this->assertEquals('Summer Special', $promotion->name);
        $this->assertEquals('percentage', $promotion->discount_type);
        $this->assertEquals(15.00, $promotion->discount_value);
    }

    #[Test]
    public function it_can_create_room_pricing()
    {
        $room = Room::create([
            'agency_id' => $this->agency->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '103',
            'floor' => 1,
            'status' => 'available',
            'is_active' => true
        ]);

        $promotion = Promotion::create([
            'agency_id' => $this->agency->id,
            'name' => 'Early Bird',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'is_active' => true
        ]);

        $pricing = RoomPricing::create([
            'agency_id' => $this->agency->id,
            'room_id' => $room->id,
            'date' => now()->addDays(30)->format('Y-m-d'),
            'rate' => 120.00,
            'currency' => 'EUR',
            'is_available' => true,
            'quota' => 1,
            'promotion_id' => $promotion->id
        ]);

        $this->assertNotNull($pricing);
        $this->assertEquals(120.00, $pricing->rate);
        $this->assertEquals('EUR', $pricing->currency);
        $this->assertEquals($promotion->id, $pricing->promotion_id);
    }

    #[Test]
    public function it_can_check_room_availability()
    {
        $room = Room::create([
            'agency_id' => $this->agency->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '104',
            'floor' => 1,
            'status' => 'available',
            'is_active' => true
        ]);

        // Test room availability function
        $this->assertTrue($room->isAvailable(now()->addDays(20)->format('Y-m-d')));
        
        // Set room to maintenance status and check availability
        $room->status = 'maintenance';
        $room->save();
        
        $this->assertFalse($room->isAvailable(now()->addDays(20)->format('Y-m-d')));
    }
}