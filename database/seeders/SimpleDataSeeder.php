<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class SimpleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin kullanıcı oluştur
        if (DB::table('users')->count() == 0) {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Otel tipleri
        $hotelTypes = [
            ['name' => 'Resort', 'description' => 'Tatil köyü tarzında otel', 'icon' => 'beach-umbrella'],
            ['name' => 'Şehir Oteli', 'description' => 'Şehir merkezinde bulunan iş oteli', 'icon' => 'building'],
            ['name' => 'Butik Otel', 'description' => 'Özel tasarımlı küçük otel', 'icon' => 'home-modern'],
        ];

        foreach ($hotelTypes as $index => $type) {
            DB::table('hotel_types')->updateOrInsert(
                ['slug' => Str::slug($type['name'])],
                [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'icon' => $type['icon'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Bölgeler placeholder - RealisticDataSeeder kullanıyoruz

        // 4. Pansiyon tipleri
        $boardTypes = [
            ['name' => 'Herşey Dahil', 'code' => 'AI', 'description' => 'Tüm yiyecek içecekler dahil'],
            ['name' => 'Tam Pansiyon', 'code' => 'FB', 'description' => 'Kahvaltı, öğle ve akşam yemeği dahil'],
            ['name' => 'Yarım Pansiyon', 'code' => 'HB', 'description' => 'Kahvaltı ve akşam yemeği dahil'],
            ['name' => 'Oda & Kahvaltı', 'code' => 'BB', 'description' => 'Sadece kahvaltı dahil'],
            ['name' => 'Sadece Oda', 'code' => 'RO', 'description' => 'Yemek dahil değil'],
        ];

        foreach ($boardTypes as $index => $type) {
            DB::table('board_types')->updateOrInsert(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'icon' => 'cake',
                    'includes' => json_encode(['Açıklama' => 'Dahili hizmetler']),
                    'excludes' => json_encode(['Açıklama' => 'Harici hizmetler']),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 5. Oda tipleri
        $roomTypes = [
            ['name' => 'Standart Oda', 'max_adults' => 2, 'max_children' => 1, 'base_price' => 1000],
            ['name' => 'Deluxe Oda', 'max_adults' => 2, 'max_children' => 2, 'base_price' => 1500],
            ['name' => 'Aile Odası', 'max_adults' => 3, 'max_children' => 2, 'base_price' => 2000],
            ['name' => 'Süit', 'max_adults' => 2, 'max_children' => 2, 'base_price' => 3000],
        ];

        foreach ($roomTypes as $index => $type) {
            $roomTypeId = DB::table('room_types')->updateOrInsert(
                ['slug' => Str::slug($type['name'])],
                [
                    'name' => $type['name'],
                    'description' => $type['name'] . ' açıklaması',
                    'short_description' => 'Kısa açıklama',
                    'max_adults' => $type['max_adults'],
                    'max_children' => $type['max_children'],
                    'max_occupancy' => $type['max_adults'] + $type['max_children'],
                    'base_price' => $type['base_price'],
                    'min_nights' => 1,
                    'size' => rand(20, 60),
                    'features' => json_encode(['Klima', 'TV', 'Mini Bar']),
                    'beds' => json_encode(['Çift Kişilik' => 1]),
                    'icon' => 'home',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            // Oda tipi-pansiyon tipi ilişkisi
            $roomTypeId = DB::table('room_types')->where('slug', Str::slug($type['name']))->value('id');
            $boardTypeIds = DB::table('board_types')->pluck('id');
            
            foreach ($boardTypeIds as $boardTypeId) {
                DB::table('room_type_board_type')->updateOrInsert(
                    [
                        'room_type_id' => $roomTypeId,
                        'board_type_id' => $boardTypeId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 6. Oda özellikleri
        $amenities = [
            ['name' => 'Klima', 'category' => 'comfort', 'icon' => 'air-conditioner'],
            ['name' => 'Mini Bar', 'category' => 'food', 'icon' => 'refrigerator'],
            ['name' => 'Ücretsiz Wi-Fi', 'category' => 'connectivity', 'icon' => 'wifi'],
            ['name' => 'LCD TV', 'category' => 'entertainment', 'icon' => 'tv'],
            ['name' => 'Saç Kurutma Makinesi', 'category' => 'bathroom', 'icon' => 'hair-dryer'],
        ];

        foreach ($amenities as $amenity) {
            DB::table('room_amenities')->updateOrInsert(
                ['slug' => Str::slug($amenity['name'])],
                [
                    'name' => $amenity['name'],
                    'category' => $amenity['category'],
                    'icon' => $amenity['icon'],
                    'description' => $amenity['name'] . ' açıklaması',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 7. Oteller
        $hotels = [
            [
                'name' => 'Antalya Resort & Spa',
                'region' => 'Antalya',
                'type' => 'Resort',
                'stars' => 5,
                'city' => 'Antalya',
                'country' => 'Türkiye',
            ],
            [
                'name' => 'İstanbul Park Hotel',
                'region' => 'İstanbul',
                'type' => 'Şehir Oteli',
                'stars' => 4,
                'city' => 'İstanbul',
                'country' => 'Türkiye',
            ],
            [
                'name' => 'Bodrum Luxury Suites',
                'region' => 'Bodrum',
                'type' => 'Butik Otel',
                'stars' => 5,
                'city' => 'Bodrum',
                'country' => 'Türkiye',
            ],
        ];

        foreach ($hotels as $hotel) {
            $regionId = DB::table('regions')->where('slug', Str::slug($hotel['region']))->value('id');
            $typeId = DB::table('hotel_types')->where('slug', Str::slug($hotel['type']))->value('id');
            
            $hotelId = DB::table('hotels')->insertGetId([
                'name' => $hotel['name'],
                'slug' => Str::slug($hotel['name']),
                'region_id' => $regionId,
                'type_id' => $typeId,
                'stars' => $hotel['stars'],
                'city' => $hotel['city'],
                'country' => $hotel['country'],
                'description' => 'Bu otelle ilgili detaylı açıklama.',
                'short_description' => 'Kısa açıklama.',
                'email' => 'info@' . Str::slug($hotel['name']) . '.com',
                'phone' => '+90 555 123 4567',
                'amenities' => json_encode(['Resepsiyon', 'Restoran', 'Spa', 'Havuz']),
                'check_in_out' => json_encode([
                    'check_in_from' => '14:00',
                    'check_in_until' => '23:59',
                    'check_out_from' => '07:00',
                    'check_out_until' => '12:00'
                ]),
                'policies' => json_encode([
                    ['title' => 'İptal Koşulları', 'description' => '24 saat öncesine kadar ücretsiz iptal.'],
                    ['title' => 'Evcil Hayvan', 'description' => 'Evcil hayvanlar kabul edilmektedir.']
                ]),
                'is_active' => true,
                'is_featured' => $hotel['stars'] >= 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // İletişim kişisi
            DB::table('hotel_contacts')->insert([
                'hotel_id' => $hotelId,
                'name' => 'Genel Müdür',
                'position' => 'Genel Müdür',
                'department' => 'Yönetim',
                'email' => 'gm@' . Str::slug($hotel['name']) . '.com',
                'phone' => '+90 555 123 4567',
                'is_primary' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Odalar
            $roomTypes = DB::table('room_types')->pluck('id');
            foreach ($roomTypes as $roomTypeId) {
                for ($i = 1; $i <= 5; $i++) {
                    $floor = rand(1, 5);
                    $roomNumber = $floor . str_pad($i, 2, '0', STR_PAD_LEFT);
                    
                    $roomData = DB::table('room_types')->where('id', $roomTypeId)->first();
                    
                    $roomId = DB::table('rooms')->insertGetId([
                        'hotel_id' => $hotelId,
                        'room_type_id' => $roomTypeId,
                        'name' => 'Oda ' . $roomNumber,
                        'room_number' => $roomNumber,
                        'floor' => $floor,
                        'max_adults' => $roomData->max_adults,
                        'max_children' => $roomData->max_children,
                        'max_occupancy' => $roomData->max_occupancy,
                        'price' => $roomData->base_price + rand(-100, 200),
                        'features' => $roomData->features,
                        'is_active' => true,
                        'is_available' => true,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Oda-Pansiyon ilişkisi
                    $boardTypeIds = DB::table('board_types')->pluck('id');
                    foreach ($boardTypeIds as $boardTypeId) {
                        DB::table('room_board_type')->insert([
                            'room_id' => $roomId,
                            'board_type_id' => $boardTypeId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    // Oda-Özellik ilişkisi
                    $amenityIds = DB::table('room_amenities')->inRandomOrder()->limit(3)->pluck('id');
                    foreach ($amenityIds as $amenityId) {
                        DB::table('room_room_amenity')->insert([
                            'room_id' => $roomId,
                            'room_amenity_id' => $amenityId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    // Her odaya 1 rezervasyon ekle
                    $checkIn = now()->addDays(rand(1, 30));
                    $nights = rand(1, 7);
                    $checkOut = (clone $checkIn)->addDays($nights);
                    
                    $reservationId = DB::table('reservations')->insertGetId([
                        'hotel_id' => $hotelId,
                        'room_id' => $roomId,
                        'reservation_number' => strtoupper(substr($hotel['name'], 0, 2)) . '-' . rand(10000, 99999),
                        'status' => 'confirmed',
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'nights' => $nights,
                        'adults' => rand(1, $roomData->max_adults),
                        'children' => rand(0, $roomData->max_children),
                        'total_price' => $roomData->base_price * $nights,
                        'currency' => 'TRY',
                        'payment_status' => 'paid',
                        'payment_method' => 'credit_card',
                        'source' => 'website',
                        'created_at' => now()->subDays(rand(1, 7)),
                        'updated_at' => now(),
                    ]);
                    
                    // Misafir
                    DB::table('guests')->insert([
                        'reservation_id' => $reservationId,
                        'first_name' => 'Misafir',
                        'last_name' => 'Kullanıcı',
                        'email' => 'guest' . rand(1, 1000) . '@example.com',
                        'phone' => '+90 555 ' . rand(1000000, 9999999),
                        'nationality' => 'TR',
                        'id_type' => 'tc_kimlik',
                        'id_number' => (string)rand(10000000000, 99999999999),
                        'birth_date' => now()->subYears(rand(25, 60)),
                        'gender' => rand(0, 1) ? 'male' : 'female',
                        'city' => $hotel['city'],
                        'country' => 'Türkiye',
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}