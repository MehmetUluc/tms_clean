<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Core\Models\Agency;
use Tms\Hotel\Models\Hotel;
use Tms\Hotel\Models\RoomType;
use Tms\Hotel\Models\BoardType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelPluginTest extends TestCase
{
    // use RefreshDatabase;

    protected $agency;

    public function setUp(): void
    {
        parent::setUp();

        // Test acentesi oluştur
        $this->agency = Agency::create([
            'name' => 'Test Agency',
            'code' => 'TEST001',
            'email' => 'test@example.com',
            'is_active' => true,
            'slug' => 'test-agency'
        ]);
    }

    #[Test]
    public function it_can_create_a_hotel()
    {
        $hotel = Hotel::create([
            'agency_id' => $this->agency->id,
            'name' => 'Luxury Resort',
            'slug' => 'luxury-resort',
            'description' => 'A beautiful resort',
            'address' => '123 Beach Road',
            'city' => 'Antalya',
            'country' => 'Turkey',
            'postal_code' => '07000',
            'phone' => '5551234567',
            'email' => 'info@luxuryresort.com',
            'website' => 'https://luxuryresort.com',
            'star_rating' => 5,
            'is_active' => true,
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'coordinates' => '36.8969, 30.7133',
            'amenities' => ['pool', 'spa', 'wifi', 'restaurant']
        ]);

        $this->assertNotNull($hotel);
        $this->assertEquals('Luxury Resort', $hotel->name);
        $this->assertEquals(5, $hotel->star_rating);
        $this->assertEquals('Antalya', $hotel->city);
    }

    #[Test]
    public function it_can_create_room_types()
    {
        $hotel = Hotel::create([
            'agency_id' => $this->agency->id,
            'name' => 'Luxury Resort',
            'slug' => 'luxury-resort',
            'address' => '123 Beach Road',
            'star_rating' => 5,
            'is_active' => true
        ]);

        $roomType = RoomType::create([
            'agency_id' => $this->agency->id,
            'hotel_id' => $hotel->id,
            'name' => 'Deluxe Room',
            'description' => 'Spacious room with sea view',
            'capacity' => 2,
            'max_adults' => 2,
            'max_children' => 1,
            'size' => 45, // m²
            'base_price' => 150.00,
            'is_active' => true
        ]);

        $this->assertNotNull($roomType);
        $this->assertEquals('Deluxe Room', $roomType->name);
        $this->assertEquals(2, $roomType->capacity);
        $this->assertEquals(150.00, $roomType->base_price);
    }

    #[Test]
    public function it_can_create_board_types()
    {
        $hotel = Hotel::create([
            'agency_id' => $this->agency->id,
            'name' => 'Luxury Resort',
            'slug' => 'luxury-resort',
            'address' => '123 Beach Road',
            'star_rating' => 5,
            'is_active' => true
        ]);

        $boardType = BoardType::create([
            'name' => 'All Inclusive',
            'code' => 'AI',
            'description' => 'All meals and drinks included',
            'is_active' => true
        ]);

        // Hotel ile ilişkisini kur
        $hotel->boardTypes()->attach($boardType->id, [
            'price_adjustment' => 50.00,
            'is_default' => true
        ]);

        $this->assertNotNull($boardType);
        $this->assertEquals('All Inclusive', $boardType->name);
        $this->assertEquals('AI', $boardType->code);
        
        // İlişkiyi kontrol et
        $this->assertCount(1, $hotel->boardTypes);
        $this->assertEquals(50.00, $hotel->boardTypes->first()->pivot->price_adjustment);
    }
}