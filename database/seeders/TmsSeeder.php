<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Tms\Agency\Models\Agency;
use Tms\Agency\Models\AgencyContract;
use Tms\Booking\Models\Guest;
use Tms\Booking\Models\Reservation;
use Tms\Hotel\Models\BoardType;
use Tms\Hotel\Models\Hotel;
use Tms\Hotel\Models\Region;
use Tms\Hotel\Models\RoomType;
use Tms\Room\Models\Room;
use Tms\Room\Models\RoomAmenity;
use Tms\Transfer\Models\Driver;
use Tms\Transfer\Models\Route;
use Tms\Transfer\Models\Transfer;
use Tms\Transfer\Models\Vehicle;

class TmsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Her paket için örnek veriler oluştur
        $this->seedAgencies();
        $this->seedHotels();
        $this->seedRooms();
        $this->seedBookings();
        $this->seedTransfers();
    }
    
    /**
     * Acente verilerini oluştur
     */
    private function seedAgencies(): void
    {
        // 5 Acente oluştur
        $agencies = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $agencies[] = Agency::create([
                'name' => "Acente {$i}",
                'code' => "AGENCY{$i}",
                'email' => "agency{$i}@example.com",
                'phone' => "+90 555 123 456{$i}",
                'website' => "https://agency{$i}.example.com",
                'contact_person' => "İletişim Kişisi {$i}",
                'address' => "Örnek Adres {$i}, İstanbul, Türkiye",
                'is_active' => true,
                'notes' => "Örnek acente notları {$i}",
            ]);
        }
        
        // Her acente için bir sözleşme oluştur
        foreach ($agencies as $agency) {
            AgencyContract::create([
                'agency_id' => $agency->id,
                'title' => "{$agency->name} Sözleşmesi",
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'commission_rate' => rand(5, 15),
                'payment_terms' => "30 gün",
                'status' => 'active',
                'notes' => "Örnek sözleşme notları",
                'created_by' => 1,
            ]);
        }
    }
    
    /**
     * Otel verilerini oluştur
     */
    private function seedHotels(): void
    {
        // 5 Bölge oluştur
        $regions = [
            'Antalya', 'Bodrum', 'İstanbul', 'Kapadokya', 'İzmir'
        ];
        
        $createdRegions = [];
        
        foreach ($regions as $index => $regionName) {
            $createdRegions[] = Region::create([
                'name' => $regionName,
                'slug' => \Illuminate\Support\Str::slug($regionName),
                'description' => "{$regionName} bölgesi açıklaması",
                'is_active' => true,
                'is_featured' => $index < 3, // İlk 3 bölge öne çıkarılmış olsun
            ]);
        }
        
        // Pansiyon tipleri oluştur
        $boardTypes = [
            'Sadece Oda', 'Oda & Kahvaltı', 'Yarım Pansiyon', 'Tam Pansiyon', 'Her Şey Dahil', 'Ultra Her Şey Dahil'
        ];
        
        $createdBoardTypes = [];
        
        foreach ($boardTypes as $index => $boardTypeName) {
            $createdBoardTypes[] = BoardType::create([
                'name' => $boardTypeName,
                'code' => strtoupper(substr(str_replace(' ', '', $boardTypeName), 0, 3)),
                'description' => "{$boardTypeName} pansiyon türü açıklaması",
                'is_active' => true,
            ]);
        }
        
        // 10 Otel oluştur
        for ($i = 1; $i <= 10; $i++) {
            $hotel = Hotel::create([
                'name' => "Örnek Otel {$i}",
                'slug' => "ornek-otel-{$i}",
                'type' => collect(['hotel', 'resort', 'boutique', 'villa', 'apartment'])->random(),
                'stars' => rand(3, 5),
                'currency' => 'TRY',
                'short_description' => "Örnek Otel {$i} kısa açıklaması",
                'description' => "Örnek Otel {$i} detaylı açıklaması. Lorem ipsum dolor sit amet...",
                'email' => "hotel{$i}@example.com",
                'phone' => "+90 555 987 654{$i}",
                'address' => "Örnek Otel Adresi {$i}",
                'city' => $createdRegions[rand(0, count($createdRegions) - 1)]->name,
                'country' => 'Türkiye',
                'is_active' => true,
                'is_featured' => $i <= 3, // İlk 3 otel öne çıkarılmış olsun
                'agency_id' => Agency::inRandomOrder()->first()->id,
                'amenities' => [
                    'wifi', 'pool', 'restaurant', 'gym', 'spa', 'bar', 'parking'
                ],
                'check_in_out' => [
                    'check_in_from' => '14:00',
                    'check_in_until' => '23:59',
                    'check_out_from' => '07:00',
                    'check_out_until' => '12:00',
                ],
                'policies' => [
                    [
                        'title' => 'Evcil Hayvan Politikası',
                        'description' => 'Evcil hayvanlar kabul edilmemektedir.',
                    ],
                    [
                        'title' => 'İptal Politikası',
                        'description' => 'Giriş tarihinden 7 gün öncesine kadar ücretsiz iptal.',
                    ],
                ],
                'gallery' => [],
                'meta_title' => "Örnek Otel {$i} - Muhteşem Tatil",
                'meta_description' => "Örnek Otel {$i} - Harika bir tatil için en iyi seçim.",
                'meta_keywords' => ["otel", "tatil", "konaklama", "örnek otel {$i}"],
            ]);
            
            // Otelin bölgelerini ata
            $hotel->regions()->attach($createdRegions[rand(0, count($createdRegions) - 1)]->id);
            
            // Oda tipleri oluştur (her otel için 3 oda tipi)
            for ($j = 1; $j <= 3; $j++) {
                $roomType = RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => ["Standart Oda", "Deluxe Oda", "Suite"][$j - 1],
                    'code' => ["STD", "DLX", "SUT"][$j - 1],
                    'description' => "Oda tipi {$j} açıklaması",
                    'max_adults' => $j + 1,
                    'max_children' => $j,
                    'size' => ($j * 10) + 20,
                    'amenities' => [
                        'tv', 'air_conditioning', 'minibar', 'safe_box', 'balcony'
                    ],
                    'is_active' => true,
                    'agency_id' => $hotel->agency_id,
                ]);
                
                // Her oda tipi için pansiyon tipleri ata
                $roomType->boardTypes()->attach(
                    $createdBoardTypes[rand(0, count($createdBoardTypes) - 1)]->id,
                    ['price' => rand(1000, 5000)]
                );
            }
        }
    }
    
    /**
     * Oda verilerini oluştur
     */
    private function seedRooms(): void
    {
        // Oda özellikleri oluştur
        $amenities = [
            'Wi-Fi', 'Klima', 'TV', 'Minibar', 'Kasa', 'Balkon', 'Deniz Manzarası', 'Banyo Küveti'
        ];
        
        $createdAmenities = [];
        
        foreach ($amenities as $amenity) {
            $createdAmenities[] = RoomAmenity::create([
                'name' => $amenity,
                'description' => "{$amenity} açıklaması",
                'icon' => 'heroicon-o-check',
                'is_active' => true,
                'agency_id' => Agency::inRandomOrder()->first()->id,
            ]);
        }
        
        // Tüm otelleri ve oda tiplerini al
        $hotels = Hotel::with('roomTypes')->get();
        
        // Her otel ve oda tipi için fiziksel odalar oluştur
        foreach ($hotels as $hotel) {
            foreach ($hotel->roomTypes as $roomType) {
                // Her oda tipi için 5 fiziksel oda oluştur
                for ($i = 1; $i <= 5; $i++) {
                    $roomNumber = "{$roomType->code}-{$i}";
                    
                    $room = Room::create([
                        'hotel_id' => $hotel->id,
                        'room_type_id' => $roomType->id,
                        'room_number' => $roomNumber,
                        'floor' => rand(1, 5),
                        'status' => collect(['available', 'occupied', 'maintenance', 'cleaning'])->random(),
                        'notes' => "Oda {$roomNumber} notları",
                        'is_active' => true,
                        'agency_id' => $hotel->agency_id,
                    ]);
                    
                    // Rastgele oda özellikleri ata
                    $room->amenities()->attach(
                        collect($createdAmenities)->random(rand(3, 5))->pluck('id')->toArray()
                    );
                }
            }
        }
    }
    
    /**
     * Rezervasyon verilerini oluştur
     */
    private function seedBookings(): void
    {
        // Mevcut acenteleri, otelleri ve odaları al
        $agencies = Agency::all();
        $rooms = Room::with('hotel', 'roomType')->get();
        
        // Rastgele 20 rezervasyon oluştur
        for ($i = 1; $i <= 20; $i++) {
            // Rastgele bir oda seç
            $room = $rooms->random();
            
            // Rezervasyon tarihleri
            $checkIn = now()->addDays(rand(1, 30));
            $checkOut = (clone $checkIn)->addDays(rand(1, 10));
            
            // Rezervasyon oluştur
            $reservation = Reservation::create([
                'booking_number' => "RES-" . str_pad($i, 6, '0', STR_PAD_LEFT),
                'hotel_id' => $room->hotel_id,
                'room_id' => $room->id,
                'room_type_id' => $room->room_type_id,
                'agency_id' => $agencies->random()->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => collect(['pending', 'confirmed', 'completed', 'cancelled'])->random(),
                'adults' => rand(1, 3),
                'children' => rand(0, 2),
                'total_price' => rand(1000, 10000),
                'currency' => 'TRY',
                'notes' => "Rezervasyon {$i} notları",
                'guest_comments' => "Misafir istekleri ve notları {$i}",
            ]);
            
            // Rezervasyon için misafir(ler) oluştur
            for ($j = 1; $j <= $reservation->adults + $reservation->children; $j++) {
                $guest = Guest::create([
                    'agency_id' => $reservation->agency_id,
                    'title' => collect(['Mr', 'Mrs', 'Ms'])->random(),
                    'first_name' => "Misafir{$j}",
                    'last_name' => "Soyad{$j}",
                    'email' => "guest{$i}{$j}@example.com",
                    'phone' => "+90 555 111 222{$j}",
                    'identity_number' => rand(10000000000, 99999999999),
                    'country' => collect(['Türkiye', 'Almanya', 'Rusya', 'İngiltere'])->random(),
                    'date_of_birth' => now()->subYears(rand(18, 70))->format('Y-m-d'),
                    'gender' => collect(['male', 'female'])->random(),
                    'is_child' => $j > $reservation->adults,
                    'notes' => "Misafir {$j} notları",
                ]);
                
                // Misafiri rezervasyona bağla
                $reservation->guests()->attach($guest->id, [
                    'is_primary' => $j === 1, // İlk misafir primary olsun
                ]);
            }
        }
    }
    
    /**
     * Transfer verilerini oluştur
     */
    private function seedTransfers(): void
    {
        // Lokasyonlar
        $locations = [
            ['name' => 'Antalya Havalimanı', 'type' => 'airport'],
            ['name' => 'Bodrum Havalimanı', 'type' => 'airport'],
            ['name' => 'İzmir Adnan Menderes Havalimanı', 'type' => 'airport'],
            ['name' => 'İstanbul Havalimanı', 'type' => 'airport'],
            ['name' => 'Antalya Otogar', 'type' => 'bus_station'],
            ['name' => 'Bodrum Merkez', 'type' => 'city_center'],
            ['name' => 'İzmir Alsancak', 'type' => 'city_center'],
            ['name' => 'İstanbul Taksim', 'type' => 'city_center'],
        ];
        
        $createdLocations = [];
        
        foreach ($locations as $locationData) {
            $createdLocations[] = \Tms\Transfer\Models\Location::create([
                'name' => $locationData['name'],
                'type' => $locationData['type'],
                'address' => "{$locationData['name']} adresi",
                'city' => explode(' ', $locationData['name'])[0],
                'country' => 'Türkiye',
                'latitude' => rand(36, 41) + (rand(0, 1000) / 1000),
                'longitude' => rand(26, 36) + (rand(0, 1000) / 1000),
                'is_active' => true,
                'agency_id' => Agency::inRandomOrder()->first()->id,
            ]);
        }
        
        // Araç tipleri oluştur
        $vehicles = [
            ['name' => 'Ekonomik Sedan', 'type' => 'sedan', 'capacity' => 4],
            ['name' => 'VIP Sedan', 'type' => 'sedan', 'capacity' => 4],
            ['name' => 'Ekonomik Van', 'type' => 'van', 'capacity' => 8],
            ['name' => 'VIP Van', 'type' => 'van', 'capacity' => 6],
            ['name' => 'Minibüs', 'type' => 'minibus', 'capacity' => 16],
            ['name' => 'Otobüs', 'type' => 'bus', 'capacity' => 30],
        ];
        
        $createdVehicles = [];
        
        foreach ($vehicles as $vehicleData) {
            $createdVehicles[] = Vehicle::create([
                'name' => $vehicleData['name'],
                'type' => $vehicleData['type'],
                'model' => "Model " . rand(2020, 2024),
                'capacity' => $vehicleData['capacity'],
                'license_plate' => rand(10, 99) . " " . strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 3)) . " " . rand(100, 999),
                'status' => collect(['active', 'maintenance', 'unavailable'])->random(),
                'notes' => "Araç notları",
                'agency_id' => Agency::inRandomOrder()->first()->id,
            ]);
        }
        
        // Sürücüler oluştur
        $drivers = [
            ['name' => 'Ahmet', 'surname' => 'Yılmaz'],
            ['name' => 'Mehmet', 'surname' => 'Özkan'],
            ['name' => 'Ayşe', 'surname' => 'Çelik'],
            ['name' => 'Fatma', 'surname' => 'Demir'],
            ['name' => 'Ali', 'surname' => 'Kaya'],
        ];
        
        $createdDrivers = [];
        
        foreach ($drivers as $driverData) {
            $createdDrivers[] = Driver::create([
                'name' => $driverData['name'],
                'surname' => $driverData['surname'],
                'license_number' => strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 2)) . rand(10000, 99999),
                'phone' => "+90 555 " . rand(100, 999) . " " . rand(10, 99) . " " . rand(10, 99),
                'email' => strtolower($driverData['name'] . "." . $driverData['surname'] . "@example.com"),
                'status' => collect(['active', 'on_leave', 'unavailable'])->random(),
                'notes' => "Sürücü notları",
                'agency_id' => Agency::inRandomOrder()->first()->id,
            ]);
        }
        
        // Rotalar oluştur
        $createdRoutes = [];
        
        for ($i = 0; $i < count($createdLocations); $i++) {
            for ($j = 0; $j < count($createdLocations); $j++) {
                if ($i !== $j) { // Aynı lokasyon için rota oluşturma
                    $createdRoutes[] = Route::create([
                        'from_location_id' => $createdLocations[$i]->id,
                        'to_location_id' => $createdLocations[$j]->id,
                        'distance' => rand(5, 100),
                        'duration' => rand(15, 120),
                        'is_active' => true,
                        'agency_id' => Agency::inRandomOrder()->first()->id,
                    ]);
                }
            }
        }
        
        // Transferler oluştur
        $hotels = Hotel::all();
        
        for ($i = 1; $i <= 20; $i++) {
            // Rastgele bir rota, araç ve sürücü seç
            $route = collect($createdRoutes)->random();
            $vehicle = collect($createdVehicles)->random();
            $driver = collect($createdDrivers)->random();
            
            // Rastgele zaman (önümüzdeki 30 gün içinde)
            $departureTime = now()->addDays(rand(1, 30))->setHour(rand(0, 23))->setMinute(rand(0, 59));
            
            Transfer::create([
                'transfer_number' => "TRF-" . str_pad($i, 6, '0', STR_PAD_LEFT),
                'route_id' => $route->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'hotel_id' => $hotels->random()->id,
                'reservation_id' => Reservation::inRandomOrder()->first()->id,
                'departure_time' => $departureTime,
                'arrival_time' => (clone $departureTime)->addMinutes($route->duration),
                'status' => collect(['pending', 'confirmed', 'completed', 'cancelled'])->random(),
                'passenger_count' => rand(1, $vehicle->capacity),
                'luggage_count' => rand(1, 5),
                'guest_name' => "Misafir Adı",
                'guest_phone' => "+90 555 " . rand(100, 999) . " " . rand(10, 99) . " " . rand(10, 99),
                'pickup_address' => "Alma adresi",
                'dropoff_address' => "Bırakma adresi",
                'price' => rand(500, 3000),
                'currency' => 'TRY',
                'notes' => "Transfer notları",
                'agency_id' => Agency::inRandomOrder()->first()->id,
            ]);
        }
    }
}