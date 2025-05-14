<?php

// Load the Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

echo "=== TMS Gerçekçi Veri Oluşturma Aracı ===\n\n";

// Enable more detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // --------- BÖLGELER ---------
    echo "Bölge verileri ekleniyor...\n";
    
    // Ülke: Türkiye varsa bul, yoksa oluştur
    $countryId = DB::table('regions')->where('slug', 'turkiye')->value('id');
    if (!$countryId) {
        $countryId = DB::table('regions')->insertGetId([
            'name' => 'Türkiye',
            'slug' => 'turkiye',
            'type' => 'country',
            'code' => 'TR',
            'description' => 'Türkiye ülkesi',
            'is_active' => true,
            'parent_id' => null,
            'meta_title' => 'Türkiye Otelleri',
            'meta_description' => 'Türkiye içindeki en iyi otel seçenekleri',
            'meta_keywords' => 'Türkiye, oteller, konaklama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    // Ana Bölgeler
    $regions = [
        'Akdeniz Bölgesi' => [
            'description' => 'Türkiye\'nin güney kıyısında yer alan, sıcak iklimi ve uzun sahilleriyle ünlü tatil bölgesi',
            'cities' => [
                'Antalya' => [
                    'description' => 'Türkiye\'nin tatil cenneti, antik kentleri ve lüks tesisleriyle ünlü şehir',
                    'districts' => ['Kemer', 'Belek', 'Lara']
                ],
                'Alanya' => [
                    'description' => 'Antalya\'nın doğusunda yer alan, kalesi ve plajlarıyla ünlü tatil beldesi',
                    'districts' => []
                ]
            ]
        ],
        'Ege Bölgesi' => [
            'description' => 'Antik kentleri, muhteşem koyları ve zeytinlikleriyle ünlü bölge',
            'cities' => [
                'İzmir' => [
                    'description' => 'Ege\'nin incisi, modern ve kozmopolit bir şehir',
                    'districts' => []
                ],
                'Muğla' => [
                    'description' => 'Bodrum, Marmaris ve Fethiye gibi önemli tatil beldelerini içeren şehir',
                    'districts' => ['Bodrum', 'Marmaris', 'Fethiye']
                ]
            ]
        ],
        'Marmara Bölgesi' => [
            'description' => 'Türkiye\'nin kuzeybatısında yer alan, İstanbul\'u da içeren çok kültürlü bölge',
            'cities' => [
                'İstanbul' => [
                    'description' => 'İki kıtayı birleştiren, tarihi ve kültürel zenginliğiyle ünlü metropol',
                    'districts' => ['Beyoğlu', 'Şişli', 'Beşiktaş']
                ]
            ]
        ]
    ];
    
    $regionIndex = 1;
    foreach ($regions as $regionName => $regionData) {
        // Var olan bölgeyi kontrol et
        $regionId = DB::table('regions')->where('slug', Str::slug($regionName))->value('id');
        if (!$regionId) {
            $regionId = DB::table('regions')->insertGetId([
                'name' => $regionName,
                'slug' => Str::slug($regionName),
                'type' => 'region',
                'description' => $regionData['description'],
                'is_active' => true,
                'parent_id' => $countryId,
                'meta_title' => $regionName . ' Otelleri',
                'meta_description' => $regionName . ' içindeki en iyi otel seçenekleri',
                'meta_keywords' => $regionName . ', oteller, konaklama',
                'sort_order' => $regionIndex,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $cityIndex = 1;
        foreach ($regionData['cities'] as $cityName => $cityData) {
            // Var olan şehri kontrol et
            $cityId = DB::table('regions')->where('slug', Str::slug($cityName))->value('id');
            if (!$cityId) {
                $cityId = DB::table('regions')->insertGetId([
                    'name' => $cityName,
                    'slug' => Str::slug($cityName),
                    'type' => 'city',
                    'description' => $cityData['description'],
                    'is_active' => true,
                    'parent_id' => $regionId,
                    'meta_title' => $cityName . ' Otelleri',
                    'meta_description' => $cityName . ' içindeki en iyi otel seçenekleri',
                    'meta_keywords' => $cityName . ', oteller, konaklama',
                    'sort_order' => $cityIndex,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $districtIndex = 1;
            foreach ($cityData['districts'] as $districtName) {
                // Var olan ilçeyi kontrol et
                $districtExists = DB::table('regions')->where('slug', Str::slug($districtName))->exists();
                if (!$districtExists) {
                    DB::table('regions')->insertGetId([
                        'name' => $districtName,
                        'slug' => Str::slug($districtName),
                        'type' => 'district',
                        'description' => $districtName . ' ilçesi açıklaması',
                        'is_active' => true,
                        'parent_id' => $cityId,
                        'meta_title' => $districtName . ' Otelleri',
                        'meta_description' => $districtName . ' içindeki en iyi otel seçenekleri',
                        'meta_keywords' => $districtName . ', oteller, konaklama',
                        'sort_order' => $districtIndex,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $districtIndex++;
            }
            $cityIndex++;
        }
        $regionIndex++;
    }
    
    // --------- OTEL TİPLERİ ---------
    echo "Otel tipleri ekleniyor...\n";
    
    $hotelTypes = [
        'Resort' => [
            'description' => 'Geniş arazilerde konumlanan tesisler',
            'icon' => 'beach-umbrella',
        ],
        'Şehir Oteli' => [
            'description' => 'Şehir merkezlerinde bulunan oteller',
            'icon' => 'building',
        ],
        'Butik Otel' => [
            'description' => 'Az odalı, özel tasarımlı oteller',
            'icon' => 'home-modern',
        ],
    ];
    
    foreach ($hotelTypes as $typeName => $typeData) {
        // Var olan otel tipini kontrol et
        $typeExists = DB::table('hotel_types')->where('slug', Str::slug($typeName))->exists();
        if (!$typeExists) {
            DB::table('hotel_types')->insertGetId([
                'name' => $typeName,
                'slug' => Str::slug($typeName),
                'description' => $typeData['description'],
                'icon' => $typeData['icon'],
                'sort_order' => array_search($typeName, array_keys($hotelTypes)) + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    // --------- OTEL ETİKETLERİ ---------
    echo "Otel etiketleri ekleniyor...\n";
    
    $hotelTags = [
        'Denize Sıfır', 'Aile Dostu', 'Spa', 'Ultra Lüks', 'Merkezde'
    ];
    
    foreach ($hotelTags as $tagName) {
        // Var olan etiketleri kontrol et
        $tagExists = DB::table('hotel_tags')->where('slug', Str::slug($tagName))->exists();
        if (!$tagExists) {
            DB::table('hotel_tags')->insertGetId([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
                'type' => 'feature',
                'icon' => 'tag',
                'is_active' => true,
                'sort_order' => array_search($tagName, $hotelTags) + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    // --------- PANSİYON TİPLERİ ---------
    echo "Pansiyon tipleri ekleniyor...\n";
    
    $boardTypes = [
        ['name' => 'Her Şey Dahil', 'code' => 'AI', 'description' => 'Tüm yiyecek içecekler dahil'],
        ['name' => 'Tam Pansiyon', 'code' => 'FB', 'description' => 'Kahvaltı, öğle ve akşam yemeği dahil'],
        ['name' => 'Yarım Pansiyon', 'code' => 'HB', 'description' => 'Kahvaltı ve akşam yemeği dahil'],
        ['name' => 'Oda & Kahvaltı', 'code' => 'BB', 'description' => 'Sadece kahvaltı dahil'],
        ['name' => 'Sadece Oda', 'code' => 'RO', 'description' => 'Yemek dahil değil'],
    ];
    
    foreach ($boardTypes as $index => $type) {
        // Var olan pansiyon tiplerini kontrol et
        $typeExists = DB::table('board_types')->where('code', $type['code'])->exists();
        if (!$typeExists) {
            DB::table('board_types')->insertGetId([
                'name' => $type['name'],
                'code' => $type['code'],
                'description' => $type['description'],
                'icon' => 'cake',
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    // --------- ODA TİPLERİ ---------
    echo "Oda tipleri ekleniyor...\n";
    
    $roomTypes = [
        ['name' => 'Standart Oda', 'base_capacity' => 2, 'max_capacity' => 3, 'max_adults' => 2, 'max_children' => 1, 'size' => 25],
        ['name' => 'Deluxe Oda', 'base_capacity' => 2, 'max_capacity' => 4, 'max_adults' => 2, 'max_children' => 2, 'size' => 35],
        ['name' => 'Aile Odası', 'base_capacity' => 3, 'max_capacity' => 5, 'max_adults' => 3, 'max_children' => 2, 'size' => 45],
        ['name' => 'Süit', 'base_capacity' => 2, 'max_capacity' => 4, 'max_adults' => 2, 'max_children' => 2, 'size' => 60],
    ];
    
    foreach ($roomTypes as $type) {
        // Var olan oda tiplerini kontrol et
        $typeExists = DB::table('room_types')->where('slug', Str::slug($type['name']))->exists();
        if (!$typeExists) {
            DB::table('room_types')->insertGetId([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'description' => $type['name'] . ' açıklaması',
                'base_capacity' => $type['base_capacity'],
                'max_capacity' => $type['max_capacity'], 
                'max_adults' => $type['max_adults'],
                'max_children' => $type['max_children'],
                'max_infants' => 1,
                'size' => (string)$type['size'],
                'features' => json_encode(['Klima', 'TV', 'Mini Bar']),
                'is_active' => true,
                'sort_order' => array_search($type, $roomTypes) + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    // --------- ODA ÖZELLİKLERİ ---------
    echo "Oda özellikleri ekleniyor...\n";
    
    $roomAmenities = [
        ['name' => 'Klima', 'category' => 'comfort', 'icon' => 'air-conditioner'],
        ['name' => 'Mini Bar', 'category' => 'food', 'icon' => 'refrigerator'],
        ['name' => 'Ücretsiz Wi-Fi', 'category' => 'connectivity', 'icon' => 'wifi'],
        ['name' => 'LCD TV', 'category' => 'entertainment', 'icon' => 'tv'],
        ['name' => 'Saç Kurutma Makinesi', 'category' => 'bathroom', 'icon' => 'hair-dryer'],
        ['name' => 'Balkon', 'category' => 'outdoor', 'icon' => 'balcony'],
    ];
    
    foreach ($roomAmenities as $index => $amenity) {
        // Var olan oda özelliklerini kontrol et
        $amenityExists = DB::table('room_amenities')->where('slug', Str::slug($amenity['name']))->exists();
        if (!$amenityExists) {
            DB::table('room_amenities')->insertGetId([
                'name' => $amenity['name'],
                'slug' => Str::slug($amenity['name']),
                'category' => $amenity['category'],
                'icon' => $amenity['icon'],
                'description' => $amenity['name'] . ' açıklaması',
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    // --------- OTEL ÖZELLİKLERİ ---------
    echo "Otel özellikleri ekleniyor...\n";
    
    $hotelAmenities = [
        ['name' => 'Açık Havuz', 'category' => 'pool', 'icon' => 'pool'],
        ['name' => 'Spa Merkezi', 'category' => 'wellness', 'icon' => 'spa'],
        ['name' => 'Fitness Merkezi', 'category' => 'fitness', 'icon' => 'dumbbell'],
        ['name' => 'Restoran', 'category' => 'dining', 'icon' => 'restaurant'],
        ['name' => 'Bar', 'category' => 'dining', 'icon' => 'cocktail'],
        ['name' => 'Çocuk Kulübü', 'category' => 'kids', 'icon' => 'kids-club'],
    ];
    
    foreach ($hotelAmenities as $index => $amenity) {
        // Var olan otel özelliklerini kontrol et
        $amenityExists = DB::table('hotel_amenities')->where('slug', Str::slug($amenity['name']))->exists();
        if (!$amenityExists) {
            DB::table('hotel_amenities')->insertGetId([
                'name' => $amenity['name'],
                'slug' => Str::slug($amenity['name']),
                'category' => $amenity['category'],
                'icon' => $amenity['icon'],
                'description' => $amenity['name'] . ' açıklaması',
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    // --------- OTELLER ---------
    echo "Oteller ekleniyor...\n";
    
    $hotels = [
        [
            'name' => 'Antalya Luxury Resort & Spa',
            'region' => 'Antalya',
            'type' => 'Resort',
            'stars' => 5,
            'tags' => ['Denize Sıfır', 'Spa']
        ],
        [
            'name' => 'İstanbul Palace Hotel',
            'region' => 'İstanbul',
            'type' => 'Şehir Oteli',
            'stars' => 5,
            'tags' => ['Merkezde', 'Ultra Lüks']
        ],
        [
            'name' => 'Bodrum Beach Resort',
            'region' => 'Bodrum',
            'type' => 'Resort',
            'stars' => 4,
            'tags' => ['Denize Sıfır', 'Aile Dostu']
        ],
    ];
    
    // Otel ID'lerini takip et
    $hotelIds = [];
    
    foreach ($hotels as $hotel) {
        // Var olan oteli kontrol et
        $hotelExists = DB::table('hotels')->where('slug', Str::slug($hotel['name']))->exists();
        if (!$hotelExists) {
            // İlgili bölgeyi bul
            $regionId = DB::table('regions')->where('name', $hotel['region'])->value('id');
            
            // Otel tipini bul
            $typeId = DB::table('hotel_types')->where('name', $hotel['type'])->value('id');
            
            // Oteli ekle
            $hotelId = DB::table('hotels')->insertGetId([
                'name' => $hotel['name'],
                'slug' => Str::slug($hotel['name']),
                'region_id' => $regionId,
                'hotel_type_id' => $typeId,
                'description' => $hotel['name'] . ' hakkında detaylı açıklama',
                'short_description' => $hotel['name'] . ' kısa açıklama',
                'star_rating' => $hotel['stars'],
                'is_active' => true,
                'is_featured' => $hotel['stars'] >= 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
            // Otel ID'sini kaydet
            $hotelIds[] = $hotelId;
            
            // Otel etiketlerini ekle
            foreach ($hotel['tags'] as $tagName) {
                $tagId = DB::table('hotel_tags')->where('name', $tagName)->value('id');
                if ($tagId) {
                    DB::table('hotel_hotel_tag')->insert([
                        'hotel_id' => $hotelId,
                        'hotel_tag_id' => $tagId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        
            // Otel özellikleri ekle
            $amenityIds = DB::table('hotel_amenities')->inRandomOrder()->limit(4)->pluck('id');
            foreach ($amenityIds as $amenityId) {
                DB::table('hotel_hotel_amenity')->insert([
                    'hotel_id' => $hotelId,
                    'hotel_amenity_id' => $amenityId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Otel iletişim bilgisi
            DB::table('hotel_contacts')->insert([
                'hotel_id' => $hotelId,
                'name' => 'Otel Müdürü',
                'position' => 'Genel Müdür',
                'department' => 'Yönetim',
                'email' => 'manager@' . Str::slug($hotel['name']) . '.com',
                'phone' => '+90 555 123 4567',
                'is_primary' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    // --------- ODALAR VE REZERVASYONLAR ---------
    echo "Odalar ve rezervasyonlar ekleniyor...\n";
    
    // Önce mevcut oda ve rezervasyonları temizle
    DB::table('guests')->delete();
    DB::table('reservations')->delete();
    DB::table('rate_plans')->delete();
    DB::table('room_board_type')->delete();
    DB::table('room_room_amenity')->delete();
    DB::table('rooms')->delete();
    
    // Oda tiplerini al
    $roomTypes = DB::table('room_types')->get();
    
    // Oda özellik ID'lerini al
    $amenityIds = DB::table('room_amenities')->pluck('id')->toArray();
    
    // Pansiyon tiplerini al
    $boardTypes = DB::table('board_types')->get();
    
    foreach ($hotelIds as $hotelId) {
        foreach ($roomTypes as $roomType) {
            // Her oda tipinden 2 oda oluştur
            for ($i = 1; $i <= 2; $i++) {
                
                echo "  {$roomType->name} {$i} oluşturuluyor...\n";
                
                $roomId = DB::table('rooms')->insertGetId([
                    'hotel_id' => $hotelId,
                    'room_type_id' => $roomType->id,
                    'name' => $roomType->name . ' ' . $i,
                    'room_number' => $roomType->id . '0' . $i,
                    'floor' => 1,
                    'description' => $roomType->name . ' tipi oda, ' . $roomType->max_adults . ' yetişkin kapasiteli',
                    'notes' => 'Oda ' . $roomType->size . ' metrekare büyüklüğünde',
                    'is_active' => true,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Oda özellikleri
                shuffle($amenityIds);
                $roomAmenities = array_slice($amenityIds, 0, 3);
                foreach ($roomAmenities as $amenityId) {
                    DB::table('room_room_amenity')->insert([
                        'room_id' => $roomId,
                        'room_amenity_id' => $amenityId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Pansiyon tipleri
                foreach ($boardTypes as $boardType) {
                    DB::table('room_board_type')->insert([
                        'room_id' => $roomId,
                        'board_type_id' => $boardType->id,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Rate plan
                    DB::table('rate_plans')->insertGetId([
                        'hotel_id' => $hotelId,
                        'room_id' => $roomId,
                        'board_type_id' => $boardType->id,
                        'is_per_person' => true,
                        'status' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Sadece ilk odaya rezervasyon ekle
                if ($i == 1) {
                    $checkInDate = now()->addDays(rand(5, 15));
                    $nights = rand(2, 7);
                    $checkOutDate = (clone $checkInDate)->addDays($nights);
                    $boardTypeId = $boardTypes->random()->id;
                    
                    $reservationId = DB::table('reservations')->insertGetId([
                        'reservation_number' => 'RES-' . rand(10000, 99999),
                        'hotel_id' => $hotelId,
                        'room_id' => $roomId,
                        'board_type_id' => $boardTypeId,
                        'check_in_date' => $checkInDate->format('Y-m-d'),
                        'check_out_date' => $checkOutDate->format('Y-m-d'),
                        'check_in_time' => '14:00:00',
                        'check_out_time' => '12:00:00',
                        'adults' => rand(1, (int)$roomType->max_adults),
                        'children' => rand(0, (int)$roomType->max_children),
                        'infants' => 0,
                        'total_price' => rand(1000, 5000),
                        'currency' => 'TRY',
                        'status' => 'confirmed',
                        'payment_status' => 'paid',
                        'source' => 'online',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Rezervasyon için misafir
                    DB::table('guests')->insert([
                        'reservation_id' => $reservationId,
                        'first_name' => 'Test',
                        'last_name' => 'Misafir' . rand(1, 100),
                        'email' => 'guest' . rand(1, 100) . '@example.com',
                        'phone' => '+90 555 ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
    
    echo "\nVeri oluşturma tamamlandı, özet bilgiler getiriliyor...\n";
    
    // Özet bilgiler
    $regionCount = DB::table('regions')->count();
    $hotelCount = DB::table('hotels')->count();
    $roomCount = DB::table('rooms')->count();
    $reservationCount = DB::table('reservations')->count();
    $ratePlanCount = DB::table('rate_plans')->count();
    $guestCount = DB::table('guests')->count();
    
    echo "Bölge sayısı: {$regionCount}\n";
    echo "Otel sayısı: {$hotelCount}\n";
    echo "Oda sayısı: {$roomCount}\n";
    echo "Rezervasyon sayısı: {$reservationCount}\n";
    echo "Rate plan sayısı: {$ratePlanCount}\n";
    echo "Misafir sayısı: {$guestCount}\n";
    
    echo "\nTüm veriler başarıyla eklendi.\n";
    
} catch (Exception $e) {
    echo "Hata: " . $e->getMessage() . "\n";
    echo "Satır: " . $e->getLine() . "\n";
    echo "Dosya: " . $e->getFile() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}