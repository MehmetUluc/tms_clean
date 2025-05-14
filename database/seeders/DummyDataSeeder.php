<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Otel tipleri oluştur
        $hotelTypeIds = [];
        $hotelTypes = [
            ['name' => 'Resort', 'description' => 'Tatil köyü tarzında otel'],
            ['name' => 'Şehir Oteli', 'description' => 'Şehir merkezinde bulunan iş oteli'],
            ['name' => 'Butik Otel', 'description' => 'Özel tasarımlı küçük otel'],
        ];

        foreach ($hotelTypes as $type) {
            // Eğer tablo yoksa, oluştur
            DB::statement('CREATE TABLE IF NOT EXISTS `hotel_types` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `slug` varchar(255) NOT NULL,
                `description` text,
                `icon` varchar(255) DEFAULT NULL,
                `sort_order` int DEFAULT 0,
                `is_active` tinyint(1) NOT NULL DEFAULT "1",
                `deleted_at` timestamp NULL DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `hotel_types_slug_unique` (`slug`)
            )');
            
            $hotelTypeIds[] = DB::table('hotel_types')->insertGetId([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'description' => $type['description'],
                'icon' => 'hotel',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Bölgeleri oluştur
        $regionIds = [];
        $regions = [
            ['name' => 'Antalya', 'description' => 'Akdeniz\'in incisi'],
            ['name' => 'İstanbul', 'description' => 'İki kıtayı birleştiren şehir'],
            ['name' => 'İzmir', 'description' => 'Ege\'nin incisi'],
            ['name' => 'Bodrum', 'description' => 'Tatil cenneti'],
        ];

        // Tabloya sütun eklemeyi dene (eğer yoksa)
        try {
            DB::statement('ALTER TABLE regions ADD COLUMN IF NOT EXISTS deleted_at timestamp NULL DEFAULT NULL');
        } catch (\Exception $e) {
            // Sorun olursa geç
        }

        foreach ($regions as $region) {
            $regionIds[] = DB::table('regions')->insertGetId([
                'name' => $region['name'],
                'slug' => Str::slug($region['name']),
                'description' => $region['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Otelleri oluştur
        $hotels = [
            [
                'name' => 'Antalya Resort & Spa',
                'region_id' => $regionIds[0],
                'type_id' => $hotelTypeIds[0],
                'stars' => 5,
                'city' => 'Antalya',
                'country' => 'Türkiye',
            ],
            [
                'name' => 'İstanbul Park Hotel',
                'region_id' => $regionIds[1],
                'type_id' => $hotelTypeIds[1],
                'stars' => 4,
                'city' => 'İstanbul',
                'country' => 'Türkiye',
            ],
            [
                'name' => 'Bodrum Luxury Suites',
                'region_id' => $regionIds[3],
                'type_id' => $hotelTypeIds[2],
                'stars' => 5,
                'city' => 'Bodrum',
                'country' => 'Türkiye',
            ],
        ];

        $hotelIds = [];
        foreach ($hotels as $hotel) {
            $hotelIds[] = DB::table('hotels')->insertGetId([
                'name' => $hotel['name'],
                'slug' => Str::slug($hotel['name']),
                'region_id' => $hotel['region_id'],
                'type_id' => $hotel['type_id'],
                'stars' => $hotel['stars'],
                'city' => $hotel['city'],
                'country' => $hotel['country'],
                'description' => 'Bu otelle ilgili detaylı açıklama.',
                'short_description' => 'Kısa açıklama.',
                'email' => 'info@' . Str::slug($hotel['name']) . '.com',
                'phone' => '+90 555 123 4567',
                'is_active' => true,
                'is_featured' => $hotel['stars'] >= 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Oda tipleri oluştur
        $roomTypes = [
            ['name' => 'Standart Oda', 'max_adults' => 2, 'max_children' => 1, 'base_price' => 1000],
            ['name' => 'Deluxe Oda', 'max_adults' => 2, 'max_children' => 2, 'base_price' => 1500],
            ['name' => 'Aile Odası', 'max_adults' => 3, 'max_children' => 2, 'base_price' => 2000],
            ['name' => 'Süit', 'max_adults' => 2, 'max_children' => 2, 'base_price' => 3000],
        ];

        // Tablo oluştur
        DB::statement('CREATE TABLE IF NOT EXISTS `room_types` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `slug` varchar(255) NOT NULL,
            `description` text,
            `short_description` varchar(255) DEFAULT NULL,
            `max_adults` int DEFAULT 2,
            `max_children` int DEFAULT 0,
            `max_occupancy` int DEFAULT 2,
            `base_price` decimal(10,2) DEFAULT 0.00,
            `min_nights` int DEFAULT 1,
            `size` int DEFAULT NULL,
            `features` text,
            `beds` text,
            `icon` varchar(255) DEFAULT NULL,
            `sort_order` int DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT "1",
            `is_featured` tinyint(1) NOT NULL DEFAULT "0",
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `room_types_slug_unique` (`slug`)
        )');

        $roomTypeIds = [];
        foreach ($roomTypes as $type) {
            $roomTypeIds[] = DB::table('room_types')->insertGetId([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
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
                'icon' => 'heroicon-o-home',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Pansiyon tipleri oluştur
        $boardTypes = [
            ['name' => 'Herşey Dahil', 'code' => 'AI', 'description' => 'Tüm yiyecek içecekler dahil'],
            ['name' => 'Tam Pansiyon', 'code' => 'FB', 'description' => 'Kahvaltı, öğle ve akşam yemeği dahil'],
            ['name' => 'Yarım Pansiyon', 'code' => 'HB', 'description' => 'Kahvaltı ve akşam yemeği dahil'],
            ['name' => 'Oda & Kahvaltı', 'code' => 'BB', 'description' => 'Sadece kahvaltı dahil'],
            ['name' => 'Sadece Oda', 'code' => 'RO', 'description' => 'Yemek dahil değil'],
        ];

        // Tablo oluştur
        DB::statement('CREATE TABLE IF NOT EXISTS `board_types` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `code` varchar(10) NOT NULL,
            `description` text,
            `icon` varchar(255) DEFAULT NULL,
            `includes` text,
            `excludes` text,
            `sort_order` int DEFAULT 0,
            `is_active` tinyint(1) NOT NULL DEFAULT "1",
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `board_types_code_unique` (`code`)
        )');

        $boardTypeIds = [];
        foreach ($boardTypes as $type) {
            $boardTypeIds[] = DB::table('board_types')->insertGetId([
                'name' => $type['name'],
                'code' => $type['code'],
                'description' => $type['description'],
                'icon' => 'heroicon-o-cake',
                'includes' => json_encode(['Açıklama' => 'Dahili hizmetler']),
                'excludes' => json_encode(['Açıklama' => 'Harici hizmetler']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Odaları oluştur
        // Tablo oluştur
        DB::statement('CREATE TABLE IF NOT EXISTS `rooms` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `hotel_id` bigint unsigned NOT NULL,
            `room_type_id` bigint unsigned NOT NULL,
            `name` varchar(255) NOT NULL,
            `room_number` varchar(50) DEFAULT NULL,
            `floor` varchar(50) DEFAULT NULL,
            `description` text,
            `max_adults` int DEFAULT 2,
            `max_children` int DEFAULT 0,
            `max_occupancy` int DEFAULT 2,
            `size` int DEFAULT NULL,
            `price` decimal(10,2) DEFAULT 0.00,
            `features` text,
            `is_active` tinyint(1) NOT NULL DEFAULT "1",
            `is_available` tinyint(1) NOT NULL DEFAULT "1",
            `is_clean` tinyint(1) NOT NULL DEFAULT "1",
            `status` varchar(50) DEFAULT "available",
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `rooms_hotel_id_foreign` (`hotel_id`),
            KEY `rooms_room_type_id_foreign` (`room_type_id`)
        )');

        // Oda-Pansiyon bağlantı tablosu oluştur
        DB::statement('CREATE TABLE IF NOT EXISTS `room_board_type` (
            `room_id` bigint unsigned NOT NULL,
            `board_type_id` bigint unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`room_id`,`board_type_id`)
        )');

        foreach ($hotelIds as $hotelId) {
            foreach ($roomTypeIds as $roomTypeId) {
                $roomCount = rand(3, 10);
                
                for ($i = 1; $i <= $roomCount; $i++) {
                    $floor = rand(1, 5);
                    $roomNumber = $floor . str_pad($i, 2, '0', STR_PAD_LEFT);
                    
                    $roomId = DB::table('rooms')->insertGetId([
                        'hotel_id' => $hotelId,
                        'room_type_id' => $roomTypeId,
                        'name' => 'Oda ' . $roomNumber,
                        'room_number' => $roomNumber,
                        'floor' => $floor,
                        'max_adults' => DB::table('room_types')->where('id', $roomTypeId)->value('max_adults'),
                        'max_children' => DB::table('room_types')->where('id', $roomTypeId)->value('max_children'),
                        'max_occupancy' => DB::table('room_types')->where('id', $roomTypeId)->value('max_occupancy'),
                        'price' => DB::table('room_types')->where('id', $roomTypeId)->value('base_price') + rand(-100, 200),
                        'features' => json_encode(['Klima', 'TV', 'Mini Bar']),
                        'is_active' => true,
                        'is_available' => true,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Oda-Pansiyon ilişkisini kur
                    foreach ($boardTypeIds as $boardTypeId) {
                        try {
                            DB::table('room_board_type')->insert([
                                'room_id' => $roomId,
                                'board_type_id' => $boardTypeId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            // İlişki zaten varsa pas geç
                        }
                    }
                }
            }
        }

        // Oda özellikleri oluştur
        $amenities = [
            ['name' => 'Klima', 'category' => 'comfort', 'icon' => 'air-conditioner'],
            ['name' => 'Mini Bar', 'category' => 'food', 'icon' => 'refrigerator'],
            ['name' => 'Ücretsiz Wi-Fi', 'category' => 'connectivity', 'icon' => 'wifi'],
            ['name' => 'LCD TV', 'category' => 'entertainment', 'icon' => 'tv'],
            ['name' => 'Saç Kurutma Makinesi', 'category' => 'bathroom', 'icon' => 'hair-dryer'],
        ];

        // Tablo oluştur
        DB::statement('CREATE TABLE IF NOT EXISTS `room_amenities` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `slug` varchar(255) NOT NULL,
            `category` varchar(50) DEFAULT NULL,
            `icon` varchar(255) DEFAULT NULL,
            `description` text,
            `is_active` tinyint(1) NOT NULL DEFAULT "1",
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `room_amenities_slug_unique` (`slug`)
        )');

        // Oda-Özellik bağlantı tablosu
        DB::statement('CREATE TABLE IF NOT EXISTS `room_room_amenity` (
            `room_id` bigint unsigned NOT NULL,
            `room_amenity_id` bigint unsigned NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`room_id`,`room_amenity_id`)
        )');

        $amenityIds = [];
        foreach ($amenities as $amenity) {
            try {
                $amenityIds[] = DB::table('room_amenities')->insertGetId([
                    'name' => $amenity['name'],
                    'slug' => Str::slug($amenity['name']),
                    'category' => $amenity['category'],
                    'icon' => $amenity['icon'],
                    'description' => $amenity['name'] . ' açıklaması',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Özellik zaten varsa atla
            }
        }

        // Oda-Özellik ilişkilerini kur
        $rooms = DB::table('rooms')->get();
        foreach ($rooms as $room) {
            try {
                $randomAmenities = array_rand(array_flip($amenityIds), min(count($amenityIds), rand(2, 5)));
                if (!is_array($randomAmenities)) {
                    $randomAmenities = [$randomAmenities];
                }
                
                foreach ($randomAmenities as $amenityId) {
                    try {
                        DB::table('room_room_amenity')->insert([
                            'room_id' => $room->id,
                            'room_amenity_id' => $amenityId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        // İlişki zaten varsa pas geç
                    }
                }
            } catch (\Exception $e) {
                // Sorun olursa pas geç
            }
        }

        // Rezervasyonlar ve misafirler oluştur
        $firstNames = ['Ali', 'Mehmet', 'Ayşe', 'Fatma', 'Mustafa', 'Ahmet', 'Zeynep'];
        $lastNames = ['Yılmaz', 'Kaya', 'Demir', 'Çelik', 'Şahin', 'Yıldız', 'Özdemir'];
        $statuses = ['confirmed', 'checked_in', 'checked_out', 'cancelled'];

        // Rezervasyon tablosu oluştur
        DB::statement('CREATE TABLE IF NOT EXISTS `reservations` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `hotel_id` bigint unsigned NOT NULL,
            `room_id` bigint unsigned NOT NULL,
            `reservation_number` varchar(50) NOT NULL,
            `status` varchar(50) NOT NULL DEFAULT "pending",
            `check_in` date NOT NULL,
            `check_out` date NOT NULL,
            `nights` int NOT NULL DEFAULT 1,
            `adults` int NOT NULL DEFAULT 1,
            `children` int NOT NULL DEFAULT 0,
            `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
            `currency` varchar(10) NOT NULL DEFAULT "TRY",
            `payment_status` varchar(50) DEFAULT "pending",
            `payment_method` varchar(50) DEFAULT NULL,
            `notes` text,
            `source` varchar(50) DEFAULT "website",
            `created_by` bigint unsigned DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `reservations_reservation_number_unique` (`reservation_number`),
            KEY `reservations_hotel_id_foreign` (`hotel_id`),
            KEY `reservations_room_id_foreign` (`room_id`)
        )');
        
        // Misafir tablosu oluştur
        DB::statement('CREATE TABLE IF NOT EXISTS `guests` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `reservation_id` bigint unsigned NOT NULL,
            `first_name` varchar(255) NOT NULL,
            `last_name` varchar(255) NOT NULL,
            `email` varchar(255) DEFAULT NULL,
            `phone` varchar(255) DEFAULT NULL,
            `nationality` varchar(50) DEFAULT NULL,
            `id_type` varchar(50) DEFAULT NULL,
            `id_number` varchar(50) DEFAULT NULL,
            `birth_date` date DEFAULT NULL,
            `gender` varchar(10) DEFAULT NULL,
            `address` text,
            `city` varchar(255) DEFAULT NULL,
            `country` varchar(255) DEFAULT NULL,
            `is_primary` tinyint(1) NOT NULL DEFAULT "0",
            `is_child` tinyint(1) NOT NULL DEFAULT "0",
            `notes` text,
            `deleted_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `guests_reservation_id_foreign` (`reservation_id`)
        )');

        foreach ($hotelIds as $hotelId) {
            $hotelRooms = DB::table('rooms')->where('hotel_id', $hotelId)->get();
            
            if ($hotelRooms->count() > 0) {
                for ($i = 0; $i < 10; $i++) {
                    $room = $hotelRooms->random();
                    
                    // Tarihler
                    $startOffset = rand(-30, 90); // -30 gün önce ile 90 gün sonrası arası
                    $checkIn = now()->addDays($startOffset)->startOfDay();
                    $nights = rand(1, 7);
                    $checkOut = (clone $checkIn)->addDays($nights);
                    
                    // Rezervasyon durumu
                    $status = $statuses[array_rand($statuses)];
                    if ($checkIn->isFuture() && in_array($status, ['checked_in', 'checked_out'])) {
                        $status = 'confirmed';
                    }
                    if ($checkOut->isPast() && $status == 'confirmed') {
                        $status = 'checked_out';
                    }
                    
                    // Kişi sayıları
                    $adults = rand(1, $room->max_adults);
                    $children = rand(0, $room->max_children);
                    
                    // Fiyat hesapla
                    $totalPrice = $room->price * $nights;
                    
                    // Rezervasyon oluştur
                    try {
                        $reservationId = DB::table('reservations')->insertGetId([
                            'hotel_id' => $hotelId,
                            'room_id' => $room->id,
                            'reservation_number' => strtoupper(substr(DB::table('hotels')->find($hotelId)->name, 0, 2)) . '-' . rand(10000, 99999),
                            'status' => $status,
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'nights' => $nights,
                            'adults' => $adults,
                            'children' => $children,
                            'total_price' => $totalPrice,
                            'currency' => 'TRY',
                            'payment_status' => rand(0, 1) ? 'paid' : 'pending',
                            'payment_method' => rand(0, 1) ? 'credit_card' : 'bank_transfer',
                            'source' => rand(0, 1) ? 'website' : 'phone',
                            'created_at' => now()->subDays(rand(1, 30)),
                            'updated_at' => now(),
                        ]);
                        
                        // Ana misafir oluştur
                        $firstName = $firstNames[array_rand($firstNames)];
                        $lastName = $lastNames[array_rand($lastNames)];
                        $email = strtolower(str_replace(' ', '', $firstName)) . '.' . strtolower(str_replace(' ', '', $lastName)) . '@example.com';
                        
                        $mainGuestId = DB::table('guests')->insertGetId([
                            'reservation_id' => $reservationId,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'email' => $email,
                            'phone' => '+90' . rand(500, 599) . rand(1000000, 9999999),
                            'nationality' => 'TR',
                            'id_type' => rand(0, 1) ? 'tc_kimlik' : 'passport',
                            'id_number' => (string)rand(10000000000, 99999999999),
                            'birth_date' => now()->subYears(rand(25, 60))->subDays(rand(0, 365)),
                            'gender' => rand(0, 1) ? 'male' : 'female',
                            'address' => 'Örnek Mahallesi, Test Sokak No:' . rand(1, 100),
                            'city' => DB::table('hotels')->find($hotelId)->city,
                            'country' => 'Türkiye',
                            'is_primary' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        // Diğer misafirleri ekle
                        if ($adults > 1 || $children > 0) {
                            // Ekstra yetişkinler
                            for ($j = 1; $j < $adults; $j++) {
                                $firstName = $firstNames[array_rand($firstNames)];
                                
                                DB::table('guests')->insert([
                                    'reservation_id' => $reservationId,
                                    'first_name' => $firstName,
                                    'last_name' => $lastName, // Aynı soyisim
                                    'nationality' => 'TR',
                                    'id_type' => rand(0, 1) ? 'tc_kimlik' : 'passport',
                                    'id_number' => (string)rand(10000000000, 99999999999),
                                    'birth_date' => now()->subYears(rand(25, 60))->subDays(rand(0, 365)),
                                    'gender' => rand(0, 1) ? 'male' : 'female',
                                    'is_primary' => false,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                            
                            // Çocuklar
                            for ($j = 0; $j < $children; $j++) {
                                $firstName = $firstNames[array_rand($firstNames)];
                                
                                DB::table('guests')->insert([
                                    'reservation_id' => $reservationId,
                                    'first_name' => $firstName,
                                    'last_name' => $lastName,
                                    'nationality' => 'TR',
                                    'id_type' => 'tc_kimlik',
                                    'id_number' => (string)rand(10000000000, 99999999999),
                                    'birth_date' => now()->subYears(rand(3, 15))->subDays(rand(0, 365)),
                                    'gender' => rand(0, 1) ? 'male' : 'female',
                                    'is_primary' => false,
                                    'is_child' => true,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        // Sorun olursa pas geç
                    }
                }
            }
        }
    }
}