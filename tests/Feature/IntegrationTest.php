<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Tms\Core\Models\Agency;
use Tms\Hotel\Models\Hotel;
use Tms\Hotel\Models\RoomType;
use Tms\Room\Models\Room;
use Tms\Booking\Models\Guest;
use Tms\Booking\Models\Reservation;
use Tms\Transfer\Models\Vehicle;
use Tms\Transfer\Models\Driver;
use Tms\Transfer\Models\Location;
use Tms\Transfer\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IntegrationTest extends TestCase
{
    // use RefreshDatabase;

    protected $agency;
    protected $hotel;
    protected $roomType;
    protected $room;
    protected $guest;

    public function setUp(): void
    {
        parent::setUp();

        // Temel modelleri oluştur
        $this->agency = Agency::create([
            'name' => 'Integrated Agency',
            'code' => 'INT001',
            'email' => 'info@integrated.com',
            'phone' => '5551234567',
            'is_active' => true,
            'slug' => 'integrated-agency'
        ]);

        $this->hotel = Hotel::create([
            'agency_id' => $this->agency->id,
            'name' => 'Beach Resort',
            'slug' => 'beach-resort',
            'address' => '123 Beach Road, Antalya',
            'star_rating' => 5,
            'is_active' => true
        ]);

        $this->roomType = RoomType::create([
            'agency_id' => $this->agency->id,
            'hotel_id' => $this->hotel->id,
            'name' => 'Deluxe Sea View',
            'capacity' => 2,
            'max_adults' => 2,
            'max_children' => 1,
            'base_price' => 120.00,
            'is_active' => true
        ]);

        $this->room = Room::create([
            'agency_id' => $this->agency->id,
            'room_type_id' => $this->roomType->id,
            'room_number' => '301',
            'floor' => 3,
            'status' => 'available',
            'is_active' => true
        ]);

        $this->guest = Guest::create([
            'agency_id' => $this->agency->id,
            'first_name' => 'John',
            'last_name' => 'Tourist',
            'email' => 'john.tourist@example.com',
            'phone' => '5559876543',
            'nationality' => 'DE'
        ]);
    }

    #[Test]
    public function it_can_create_a_complete_booking_with_transfer()
    {
        // 1. Rezervasyon oluştur
        $reservation = Reservation::create([
            'agency_id' => $this->agency->id,
            'reservation_code' => 'INT-001',
            'hotel_id' => $this->hotel->id,
            'guest_id' => $this->guest->id,
            'check_in_date' => now()->addDays(10)->format('Y-m-d'),
            'check_out_date' => now()->addDays(15)->format('Y-m-d'),
            'nights' => 5,
            'adults' => 2,
            'children' => 0,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'total_amount' => 600.00,
            'tax_amount' => 60.00,
            'grand_total' => 660.00,
            'paid_amount' => 660.00,
            'currency' => 'EUR',
            'special_requests' => 'High floor, late check-in'
        ]);

        // 2. Rezervasyon-Oda ilişkisini kur
        $reservation->rooms()->attach($this->room->id, [
            'room_type_id' => $this->roomType->id,
            'rate' => 120.00,
            'adults' => 2,
            'children' => 0
        ]);

        // 3. Transfer için lokasyon oluştur
        $airportLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Airport',
            'type' => 'airport',
            'is_active' => true
        ]);

        $hotelLocation = Location::create([
            'agency_id' => $this->agency->id,
            'name' => 'Beach Resort',
            'type' => 'hotel',
            'is_active' => true
        ]);

        // 4. Transfer için araç ve şoför oluştur
        $vehicle = Vehicle::create([
            'agency_id' => $this->agency->id,
            'name' => 'Luxury Sedan',
            'type' => 'sedan',
            'capacity' => 4,
            'is_active' => true
        ]);

        $driver = Driver::create([
            'agency_id' => $this->agency->id,
            'name' => 'Driver Name',
            'phone' => '5551112233',
            'is_active' => true
        ]);

        // 5. Transfer oluştur
        $transfer = Transfer::create([
            'agency_id' => $this->agency->id,
            'reference_code' => 'TRF-INT-001',
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'customer_name' => $this->guest->first_name . ' ' . $this->guest->last_name,
            'customer_phone' => $this->guest->phone,
            'customer_email' => $this->guest->email,
            'pickup_datetime' => now()->addDays(10)->format('Y-m-d') . ' 10:00:00',
            'pickup_location' => 'Airport Arrival Terminal',
            'dropoff_location' => 'Beach Resort Hotel',
            'passenger_count' => 2,
            'status' => 'confirmed',
            'price' => 30.00,
            'currency' => 'EUR',
            'notes' => 'Flight: LH1234, Arriving at 09:30'
        ]);

        // Tüm ilişkili modelleri kontrol et
        $this->assertNotNull($reservation);
        $this->assertNotNull($transfer);
        
        // Rezervasyon ve oda ilişkisini doğrula
        $this->assertCount(1, $reservation->rooms);
        $this->assertEquals($this->room->id, $reservation->rooms->first()->id);
        
        // Oda durumunu kontrol et
        $room = Room::find($this->room->id);
        
        // Entegrasyon ilişkilerini doğrula - Misafir ve Otel
        $this->assertEquals($this->guest->id, $reservation->guest_id);
        $this->assertEquals($this->hotel->id, $reservation->hotel_id);
        
        // Transfer ve misafir ilişkisini doğrula
        $this->assertEquals($this->guest->email, $transfer->customer_email);
        $this->assertEquals($this->guest->phone, $transfer->customer_phone);

        // Test: Rezervasyonun giriş tarihi, transfer tarihiyle eşleşiyor mu?
        $reservationDate = substr($reservation->check_in_date, 0, 10);
        $transferDate = substr($transfer->pickup_datetime, 0, 10);
        $this->assertEquals($reservationDate, $transferDate);
    }

    #[Test]
    public function tenant_filtering_works_correctly()
    {
        // İkinci bir acente oluştur
        $secondAgency = Agency::create([
            'name' => 'Second Agency',
            'code' => 'SEC001',
            'email' => 'info@second.com',
            'is_active' => true,
            'slug' => 'second-agency'
        ]);

        // İkinci acenteye ait bir otel oluştur
        $secondHotel = Hotel::create([
            'agency_id' => $secondAgency->id,
            'name' => 'Second Hotel',
            'slug' => 'second-hotel',
            'address' => 'Another Address',
            'star_rating' => 4,
            'is_active' => true
        ]);

        // İlk tenantı ayarla
        session(['tenant_id' => $this->agency->id]);
        
        // Otelleri sorgula - sadece mevcut tenant'a ait oteller gelmeli
        $hotels = Hotel::all();
        $this->assertEquals(1, $hotels->count());
        $this->assertEquals('Beach Resort', $hotels->first()->name);
        
        // İkinci tenantı ayarla
        session(['tenant_id' => $secondAgency->id]);
        
        // Otelleri sorgula - sadece ikinci tenant'a ait oteller gelmeli
        $hotels = Hotel::all();
        $this->assertEquals(1, $hotels->count());
        $this->assertEquals('Second Hotel', $hotels->first()->name);
        
        // Tenant filtresini kaldır
        session()->forget('tenant_id');
        
        // Agency modelinin tenant filtrelemesi olmadığını doğrula
        $allAgencies = Agency::all();
        $this->assertEquals(2, $allAgencies->count());
    }
}