<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Core\Models\Agency;
use Tms\Transfer\Models\Vehicle;
use Tms\Transfer\Models\Driver;
use Tms\Transfer\Models\Location;
use Tms\Transfer\Models\Route;
use Tms\Transfer\Models\RoutePrice;
use Tms\Transfer\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransferPluginTest extends TestCase
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
    public function it_can_create_a_vehicle()
    {
        $vehicle = Vehicle::create([
            'agency_id' => $this->agency->id,
            'name' => 'Mercedes Sprinter',
            'type' => 'van',
            'capacity' => 12,
            'license_plate' => 'ABC123',
            'model_year' => 2022,
            'is_active' => true,
            'features' => ['air_conditioning', 'wifi', 'leather_seats'],
            'notes' => 'VIP vehicle'
        ]);

        $this->assertNotNull($vehicle);
        $this->assertEquals('Mercedes Sprinter', $vehicle->name);
        $this->assertEquals('van', $vehicle->type);
        $this->assertEquals(12, $vehicle->capacity);
    }

    #[Test]
    public function it_can_create_a_driver()
    {
        $driver = Driver::create([
            'agency_id' => $this->agency->id,
            'name' => 'John Smith',
            'phone' => '5551234567',
            'email' => 'john@example.com',
            'license_number' => 'DL12345',
            'license_expiry_date' => now()->addYears(2)->format('Y-m-d'),
            'is_active' => true,
            'languages' => ['english', 'turkish', 'german'],
            'notes' => 'Experienced driver'
        ]);

        $this->assertNotNull($driver);
        $this->assertEquals('John Smith', $driver->name);
        $this->assertEquals('5551234567', $driver->phone);
        $this->assertTrue(in_array('turkish', $driver->languages));
    }

    #[Test]
    public function it_can_create_a_location()
    {
        $location = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Antalya Airport',
            'type' => 'airport',
            'code' => 'AYT',
            'address' => 'Antalya, Turkey',
            'coordinates' => '36.9013, 30.7971',
            'is_active' => true
        ]);

        $this->assertNotNull($location);
        $this->assertEquals('Antalya Airport', $location->name);
        $this->assertEquals('airport', $location->type);
        $this->assertEquals('AYT', $location->code);
    }

    #[Test]
    public function it_can_create_a_route()
    {
        $departureLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Antalya Airport',
            'type' => 'airport',
            'code' => 'AYT',
            'is_active' => true
        ]);

        $arrivalLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Kemer Resort Area',
            'type' => 'area',
            'is_active' => true
        ]);

        $route = Route::create([
            'agency_id' => $this->agency->id,
            'name' => 'Antalya Airport to Kemer',
            'departure_location_id' => $departureLocation->id,
            'arrival_location_id' => $arrivalLocation->id,
            'distance' => 42, // km
            'estimated_duration' => 60, // minutes
            'is_active' => true
        ]);

        $this->assertNotNull($route);
        $this->assertEquals('Antalya Airport to Kemer', $route->name);
        $this->assertEquals(42, $route->distance);
        $this->assertEquals(60, $route->estimated_duration);
    }

    #[Test]
    public function it_can_create_route_prices()
    {
        $departureLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Antalya Airport',
            'type' => 'airport',
            'is_active' => true
        ]);

        $arrivalLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Belek Resort Area',
            'type' => 'area',
            'is_active' => true
        ]);

        $route = Route::create([
            'agency_id' => $this->agency->id,
            'name' => 'Antalya Airport to Belek',
            'departure_location_id' => $departureLocation->id,
            'arrival_location_id' => $arrivalLocation->id,
            'distance' => 35,
            'estimated_duration' => 45,
            'is_active' => true
        ]);

        $routePrice = RoutePrice::create([
            'agency_id' => $this->agency->id,
            'route_id' => $route->id,
            'vehicle_type' => 'sedan',
            'max_passengers' => 4,
            'price' => 30.00,
            'currency' => 'EUR',
            'is_one_way' => true,
            'is_active' => true
        ]);

        $this->assertNotNull($routePrice);
        $this->assertEquals('sedan', $routePrice->vehicle_type);
        $this->assertEquals(4, $routePrice->max_passengers);
        $this->assertEquals(30.00, $routePrice->price);
    }

    #[Test]
    public function it_can_create_a_transfer()
    {
        $departureLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Antalya Airport',
            'type' => 'airport',
            'is_active' => true
        ]);

        $arrivalLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Lara Beach Hotels',
            'type' => 'area',
            'is_active' => true
        ]);

        $route = Route::create([
            'agency_id' => $this->agency->id,
            'name' => 'Antalya Airport to Lara',
            'departure_location_id' => $departureLocation->id,
            'arrival_location_id' => $arrivalLocation->id,
            'distance' => 15,
            'estimated_duration' => 25,
            'is_active' => true
        ]);

        $vehicle = Vehicle::create([
            'agency_id' => $this->agency->id,
            'name' => 'Standard Sedan',
            'type' => 'sedan',
            'capacity' => 4,
            'is_active' => true
        ]);

        $driver = Driver::create([
            'agency_id' => $this->agency->id,
            'name' => 'Ali Driver',
            'phone' => '5551112233',
            'is_active' => true
        ]);

        $transfer = Transfer::create([
            'agency_id' => $this->agency->id,
            'reference_code' => 'TRF001',
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'customer_name' => 'Tourist Family',
            'customer_phone' => '5559876543',
            'customer_email' => 'tourist@example.com',
            'pickup_datetime' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'pickup_location' => 'Antalya Airport Terminal 1 Exit',
            'dropoff_location' => 'Lara Barut Collection Hotel',
            'passenger_count' => 3,
            'luggage_count' => 4,
            'notes' => 'Flight TK1234 landing at 14:00',
            'status' => 'confirmed',
            'price' => 25.00,
            'currency' => 'EUR',
            'is_paid' => false
        ]);

        $this->assertNotNull($transfer);
        $this->assertEquals('TRF001', $transfer->reference_code);
        $this->assertEquals('Tourist Family', $transfer->customer_name);
        $this->assertEquals(3, $transfer->passenger_count);
        $this->assertEquals('confirmed', $transfer->status);
    }
}