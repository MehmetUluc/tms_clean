<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// Kullanıcı oluştur
$adminUserId = DB::table('users')->insertGetId([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'created_at' => now(),
    'updated_at' => now(),
]);

// Rol oluştur
$adminRoleId = DB::table('roles')->insertGetId([
    'name' => 'Admin',
    'guard_name' => 'web',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Otel tipleri oluştur
$hotelTypeIds = [];
$hotelTypes = [
    ['name' => 'Resort', 'description' => 'Tatil köyü tarzında otel'],
    ['name' => 'Şehir Oteli', 'description' => 'Şehir merkezinde bulunan iş oteli'],
    ['name' => 'Butik Otel', 'description' => 'Özel tasarımlı küçük otel'],
];

foreach ($hotelTypes as $type) {
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
                DB::table('room_board_type')->insert([
                    'room_id' => $roomId,
                    'board_type_id' => $boardTypeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
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

$amenityIds = [];
foreach ($amenities as $amenity) {
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
}

// Oda-Özellik ilişkilerini kur
$rooms = DB::table('rooms')->get();
foreach ($rooms as $room) {
    $randomAmenities = array_rand(array_flip($amenityIds), rand(2, 5));
    if (!is_array($randomAmenities)) {
        $randomAmenities = [$randomAmenities];
    }
    
    foreach ($randomAmenities as $amenityId) {
        DB::table('room_room_amenity')->insert([
            'room_id' => $room->id,
            'room_amenity_id' => $amenityId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

// Rezervasyonlar ve misafirler oluştur
$firstNames = ['Ali', 'Mehmet', 'Ayşe', 'Fatma', 'Mustafa', 'Ahmet', 'Zeynep'];
$lastNames = ['Yılmaz', 'Kaya', 'Demir', 'Çelik', 'Şahin', 'Yıldız', 'Özdemir'];
$statuses = ['confirmed', 'checked_in', 'checked_out', 'cancelled'];

foreach ($hotelIds as $hotelId) {
    $hotelRooms = DB::table('rooms')->where('hotel_id', $hotelId)->get();
    
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
    }
}

echo "Dummy veriler başarıyla eklendi!\n";