<?php

namespace Database\Seeders;

use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Amenities\Models\RoomAmenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tüm otelleri getir
        $hotels = Hotel::all();
        
        // Tüm oda tiplerini getir
        $roomTypes = RoomType::all();
        
        // Tüm oda özelliklerini getir
        $roomAmenities = RoomAmenity::all();
        
        foreach ($hotels as $hotel) {
            // Her otel için her oda tipinden rastgele sayıda odalar oluştur
            foreach ($roomTypes as $roomType) {
                // Bu oda tipinden kaç oda olacağını belirle
                $roomCount = rand(3, 10);
                
                for ($i = 1; $i <= $roomCount; $i++) {
                    // Oda numarası oluştur (örn: 101, 102, 201, 202, ...)
                    $floor = rand(1, 5);
                    $roomNumber = $floor . str_pad($i, 2, '0', STR_PAD_LEFT);
                    
                    // Özellikler için oda tipinin özelliklerini kullan ama rastgele bazılarını seç
                    $features = json_decode($roomType->features, true);
                    $selectedFeatures = array_slice($features, 0, rand(count($features) - 2, count($features)));
                    
                    // Oda oluştur
                    $room = Room::create([
                        'hotel_id' => $hotel->id,
                        'room_type_id' => $roomType->id,
                        'name' => $roomType->name . ' ' . $roomNumber,
                        'room_number' => $roomNumber,
                        'floor' => $floor,
                        'description' => $roomType->description,
                        'max_adults' => $roomType->max_adults,
                        'max_children' => $roomType->max_children,
                        'max_occupancy' => $roomType->max_occupancy,
                        'size' => $roomType->size + rand(-3, 3), // Küçük varyasyonlar ekle
                        'price' => $roomType->base_price + rand(-100, 200), // Fiyatta küçük varyasyonlar
                        'features' => $selectedFeatures,
                        'is_active' => true,
                        'is_available' => true,
                        'is_clean' => true,
                        'status' => 'available',
                    ]);
                    
                    // Rastgele oda özelliklerini ekle
                    $amenityIds = $roomAmenities->random(rand(5, 10))->pluck('id')->toArray();
                    $room->amenities()->attach($amenityIds);
                    
                    // Oda tipinin pansiyon tiplerini odaya da ekle
                    $boardTypeIds = $roomType->boardTypes->pluck('id')->toArray();
                    $room->boardTypes()->attach($boardTypeIds);
                }
            }
        }
    }
}