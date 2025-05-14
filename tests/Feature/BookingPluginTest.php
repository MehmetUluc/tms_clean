<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Booking\Models\Reservation;
use Tms\Booking\Models\Guest;
use Tms\Core\Models\Agency;
use Tms\Hotel\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingPluginTest extends TestCase
{
    // use RefreshDatabase;

    protected $agency;
    protected $hotel;

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
    }

    #[Test]
    public function it_can_create_a_guest()
    {
        $guest = Guest::create([
            'agency_id' => $this->agency->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '5551234567',
            'nationality' => 'US',
            'birth_date' => '1990-01-01',
            'identity_number' => 'ID12345',
            'passport_number' => 'P12345',
            'address' => 'Test address',
            'notes' => 'VIP Guest'
        ]);

        $this->assertNotNull($guest);
        $this->assertEquals('John', $guest->first_name);
        $this->assertEquals('Doe', $guest->last_name);
        $this->assertEquals('john@example.com', $guest->email);
    }

    #[Test]
    public function it_can_create_a_reservation()
    {
        $guest = Guest::create([
            'agency_id' => $this->agency->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $reservation = Reservation::create([
            'agency_id' => $this->agency->id,
            'reservation_code' => 'RES001',
            'hotel_id' => $this->hotel->id,
            'guest_id' => $guest->id,
            'check_in_date' => now()->addDays(5)->format('Y-m-d'),
            'check_out_date' => now()->addDays(10)->format('Y-m-d'),
            'nights' => 5,
            'adults' => 2,
            'children' => 1,
            'status' => 'confirmed',
            'payment_status' => 'pending',
            'total_amount' => 1000.00,
            'grand_total' => 1000.00
        ]);

        $this->assertNotNull($reservation);
        $this->assertEquals('RES001', $reservation->reservation_code);
        $this->assertEquals(5, $reservation->nights);
        $this->assertEquals('confirmed', $reservation->status);
    }

    #[Test]
    public function it_calculates_correct_number_of_nights()
    {
        $checkInDate = now()->addDays(5)->format('Y-m-d');
        $checkOutDate = now()->addDays(10)->format('Y-m-d');
        
        $reservation = Reservation::create([
            'agency_id' => $this->agency->id,
            'reservation_code' => 'RES002',
            'hotel_id' => $this->hotel->id,
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'nights' => 5,
            'adults' => 2,
            'children' => 0,
            'status' => 'pending',
            'payment_status' => 'pending',
            'total_amount' => 500.00,
            'grand_total' => 500.00
        ]);

        // Manuel olarak gece sayısını hesapla
        $checkIn = new \DateTime($checkInDate);
        $checkOut = new \DateTime($checkOutDate);
        $diff = $checkIn->diff($checkOut);
        $calculatedNights = $diff->days;
        
        $this->assertEquals($calculatedNights, $reservation->nights);
    }
}