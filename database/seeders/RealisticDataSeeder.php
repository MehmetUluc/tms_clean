<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class RealisticDataSeeder extends Seeder
{
    /**
     * Gerçekçi veri oluşturma
     */
    public function run(): void
    {
        // -------------------- BÖLGE HİYERARŞİSİ --------------------
        
        // Ülkeler
        $countries = [
            ['name' => 'Türkiye', 'type' => 'country', 'code' => 'TR', 'is_featured' => true, 'sort_order' => 1],
        ];
        
        foreach ($countries as $country) {
            $countryId = DB::table('regions')->insertGetId(
                array_merge([
                    'slug' => Str::slug($country['name']),
                    'description' => $country['name'] . ' ülkesi',
                    'is_active' => true,
                    'parent_id' => null,
                    'meta_title' => $country['name'] . ' Otelleri',
                    'meta_description' => $country['name'] . ' içindeki en iyi otel seçenekleri',
                    'meta_keywords' => $country['name'] . ', oteller, konaklama',
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $country)
            );
            
            // Ana Bölgeler
            $regions = [
                [
                    'name' => 'Akdeniz Bölgesi', 
                    'type' => 'region', 
                    'description' => 'Türkiye\'nin güney kıyısında yer alan, sıcak iklimi ve uzun sahilleriyle ünlü tatil bölgesi',
                    'is_featured' => true,
                    'sort_order' => 1
                ],
                [
                    'name' => 'Ege Bölgesi', 
                    'type' => 'region', 
                    'description' => 'Antik kentleri, muhteşem koyları ve zeytinlikleriyle ünlü bölge',
                    'is_featured' => true,
                    'sort_order' => 2
                ],
                [
                    'name' => 'Marmara Bölgesi', 
                    'type' => 'region', 
                    'description' => 'Türkiye\'nin kuzeybatısında yer alan, İstanbul\'u da içeren çok kültürlü bölge',
                    'is_featured' => true,
                    'sort_order' => 3
                ],
            ];
            
            foreach ($regions as $region) {
                $regionId = DB::table('regions')->insertGetId(
                    array_merge([
                        'slug' => Str::slug($region['name']),
                        'parent_id' => $countryId,
                        'is_active' => true,
                        'meta_title' => $region['name'] . ' Otelleri',
                        'meta_description' => $region['name'] . ' içindeki en iyi otel seçenekleri',
                        'meta_keywords' => $region['name'] . ', oteller, konaklama',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], $region)
                );
                
                // Şehirler
                $cities = [];
                
                if ($region['name'] === 'Akdeniz Bölgesi') {
                    $cities = [
                        [
                            'name' => 'Antalya', 
                            'type' => 'city', 
                            'description' => 'Türkiye\'nin tatil cenneti, antik kentleri ve lüks tesisleriyle ünlü şehir',
                            'latitude' => 36.8969,
                            'longitude' => 30.7133,
                            'timezone' => 'Europe/Istanbul',
                            'is_featured' => true,
                            'sort_order' => 1
                        ],
                        [
                            'name' => 'Alanya', 
                            'type' => 'city', 
                            'description' => 'Antalya\'nın doğusunda yer alan, kalesi ve plajlarıyla ünlü tatil beldesi',
                            'latitude' => 36.5427,
                            'longitude' => 31.9997,
                            'timezone' => 'Europe/Istanbul',
                            'is_featured' => true,
                            'sort_order' => 2
                        ],
                    ];
                } elseif ($region['name'] === 'Ege Bölgesi') {
                    $cities = [
                        [
                            'name' => 'İzmir', 
                            'type' => 'city', 
                            'description' => 'Ege\'nin incisi, modern ve kozmopolit bir şehir',
                            'latitude' => 38.4237,
                            'longitude' => 27.1428,
                            'timezone' => 'Europe/Istanbul',
                            'is_featured' => true,
                            'sort_order' => 1
                        ],
                        [
                            'name' => 'Muğla', 
                            'type' => 'city', 
                            'description' => 'Bodrum, Marmaris ve Fethiye gibi önemli tatil beldelerini içeren şehir',
                            'latitude' => 37.2153,
                            'longitude' => 28.3636,
                            'timezone' => 'Europe/Istanbul',
                            'is_featured' => true,
                            'sort_order' => 2
                        ],
                    ];
                } elseif ($region['name'] === 'Marmara Bölgesi') {
                    $cities = [
                        [
                            'name' => 'İstanbul', 
                            'type' => 'city', 
                            'description' => 'İki kıtayı birleştiren, tarihi ve kültürel zenginliğiyle ünlü metropol',
                            'latitude' => 41.0082,
                            'longitude' => 28.9784,
                            'timezone' => 'Europe/Istanbul',
                            'is_featured' => true,
                            'sort_order' => 1
                        ],
                        [
                            'name' => 'Bursa', 
                            'type' => 'city', 
                            'description' => 'Osmanlı\'nın ilk başkenti, termal kaynakları ve yeşiliyle ünlü şehir',
                            'latitude' => 40.1885,
                            'longitude' => 29.0610,
                            'timezone' => 'Europe/Istanbul',
                            'is_featured' => false,
                            'sort_order' => 2
                        ],
                    ];
                }
                
                foreach ($cities as $city) {
                    $cityId = DB::table('regions')->insertGetId(
                        array_merge([
                            'slug' => Str::slug($city['name']),
                            'parent_id' => $regionId,
                            'is_active' => true,
                            'meta_title' => $city['name'] . ' Otelleri',
                            'meta_description' => $city['name'] . ' içindeki en iyi otel seçenekleri',
                            'meta_keywords' => $city['name'] . ', oteller, konaklama',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ], $city)
                    );
                    
                    // İlçeler
                    $districts = [];
                    
                    if ($city['name'] === 'Antalya') {
                        $districts = [
                            [
                                'name' => 'Kemer', 
                                'type' => 'district', 
                                'description' => 'Doğal güzellikleri, plajları ve lüks otelleriyle ünlü tatil merkezi',
                                'latitude' => 36.5981,
                                'longitude' => 30.5642,
                                'is_featured' => true
                            ],
                            [
                                'name' => 'Belek', 
                                'type' => 'district', 
                                'description' => 'Golf sahaları ve lüks resortlarıyla ünlü turizm bölgesi',
                                'latitude' => 36.8635,
                                'longitude' => 31.0519,
                                'is_featured' => true
                            ],
                            [
                                'name' => 'Lara', 
                                'type' => 'district', 
                                'description' => 'Antalya merkeze yakın, uzun plajları ve lüks otelleriyle bilinen bölge',
                                'latitude' => 36.8552,
                                'longitude' => 30.7904,
                                'is_featured' => true
                            ],
                        ];
                    } elseif ($city['name'] === 'Muğla') {
                        $districts = [
                            [
                                'name' => 'Bodrum', 
                                'type' => 'district', 
                                'description' => 'Canlı gece hayatı, mavi bayraklı koyları ve lüks marinasıyla ünlü yarımada',
                                'latitude' => 37.0382,
                                'longitude' => 27.4241,
                                'is_featured' => true
                            ],
                            [
                                'name' => 'Marmaris', 
                                'type' => 'district', 
                                'description' => 'Tekne turları, uzun sahili ve çarşısıyla ünlü turistik ilçe',
                                'latitude' => 36.8552,
                                'longitude' => 28.2705,
                                'is_featured' => true
                            ],
                            [
                                'name' => 'Fethiye', 
                                'type' => 'district', 
                                'description' => 'Ölüdeniz, Kelebekler Vadisi gibi doğal güzellikleriyle ünlü bölge',
                                'latitude' => 36.6542,
                                'longitude' => 29.1254,
                                'is_featured' => true
                            ],
                        ];
                    } elseif ($city['name'] === 'İstanbul') {
                        $districts = [
                            [
                                'name' => 'Beyoğlu', 
                                'type' => 'district', 
                                'description' => 'İstiklal Caddesi, Galata Kulesi ve tarihi yapılarıyla ünlü merkezi ilçe',
                                'latitude' => 41.0319,
                                'longitude' => 28.9775,
                                'is_featured' => true
                            ],
                            [
                                'name' => 'Şişli', 
                                'type' => 'district', 
                                'description' => 'Nişantaşı gibi lüks semtleriyle bilinen, iş ve alışveriş merkezi',
                                'latitude' => 41.0611,
                                'longitude' => 28.9876,
                                'is_featured' => true
                            ],
                            [
                                'name' => 'Beşiktaş', 
                                'type' => 'district', 
                                'description' => 'Boğaz kıyısında, canlı gece hayatı ve tarihi yalılarıyla bilinen ilçe',
                                'latitude' => 41.0425,
                                'longitude' => 29.0097,
                                'is_featured' => true
                            ],
                        ];
                    }
                    
                    foreach ($districts as $district) {
                        DB::table('regions')->insertGetId(
                            array_merge([
                                'slug' => Str::slug($district['name']),
                                'parent_id' => $cityId,
                                'is_active' => true,
                                'sort_order' => array_search($district, $districts) + 1,
                                'meta_title' => $district['name'] . ' Otelleri',
                                'meta_description' => $district['name'] . ' içindeki en iyi otel seçenekleri',
                                'meta_keywords' => $district['name'] . ', oteller, konaklama',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ], $district)
                        );
                    }
                }
            }
        }

        // -------------------- OTEL TİPLERİ --------------------
        
        $hotelTypes = [
            [
                'name' => 'Resort', 
                'description' => 'Geniş arazilerde konumlanan, çeşitli aktivite ve eğlence olanakları sunan, genellikle her şey dahil konseptiyle hizmet veren lüks tesisler',
                'icon' => 'beach-umbrella', 
                'sort_order' => 1
            ],
            [
                'name' => 'Şehir Oteli', 
                'description' => 'Şehir merkezlerinde bulunan, iş seyahatleri ve kısa konaklamalar için uygun, toplantı ve konferans olanakları sunan oteller',
                'icon' => 'building', 
                'sort_order' => 2
            ],
            [
                'name' => 'Butik Otel', 
                'description' => 'Az odalı, özel tasarımlı, genellikle tarihi bir yapıda hizmet veren, kişiye özel hizmet anlayışını benimseyen küçük ve şık oteller',
                'icon' => 'home-modern', 
                'sort_order' => 3
            ],
            [
                'name' => 'Apart Otel', 
                'description' => 'Mutfak ve oturma alanı bulunan, uzun süreli konaklamalar için uygun, ev konforunu sunan konaklama tesisleri',
                'icon' => 'apartment', 
                'sort_order' => 4
            ],
            [
                'name' => 'Termal Otel', 
                'description' => 'Termal su kaynaklarına yakın konumlanmış, spa ve sağlık hizmetleri sunan, şifalı sularıyla tedavi ve dinlenme imkanı veren tesisler',
                'icon' => 'hot-spring', 
                'sort_order' => 5
            ],
        ];

        foreach ($hotelTypes as $type) {
            DB::table('hotel_types')->insertGetId([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'description' => $type['description'],
                'icon' => $type['icon'],
                'sort_order' => $type['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------- OTEL ETİKETLERİ --------------------
        
        $hotelTags = [
            [
                'name' => 'Denize Sıfır', 
                'type' => 'location', 
                'icon' => 'ocean', 
                'is_featured' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Aile Dostu', 
                'type' => 'experience', 
                'icon' => 'family', 
                'is_featured' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Romantik', 
                'type' => 'experience', 
                'icon' => 'heart', 
                'is_featured' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Spa', 
                'type' => 'facility', 
                'icon' => 'spa', 
                'is_featured' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Golf', 
                'type' => 'activity', 
                'icon' => 'golf', 
                'is_featured' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Ultra Lüks', 
                'type' => 'quality', 
                'icon' => 'crown', 
                'is_featured' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Merkezde', 
                'type' => 'location', 
                'icon' => 'map-marker', 
                'is_featured' => false,
                'sort_order' => 7
            ],
            [
                'name' => 'Evcil Hayvan Dostu', 
                'type' => 'feature', 
                'icon' => 'pet', 
                'is_featured' => false,
                'sort_order' => 8
            ],
            [
                'name' => 'Toplantı Odaları', 
                'type' => 'facility', 
                'icon' => 'conference', 
                'is_featured' => false,
                'sort_order' => 9
            ],
            [
                'name' => 'Suit Odalar', 
                'type' => 'room', 
                'icon' => 'room-service', 
                'is_featured' => false,
                'sort_order' => 10
            ],
        ];

        foreach ($hotelTags as $tag) {
            DB::table('hotel_tags')->insertGetId([
                'name' => $tag['name'],
                'slug' => Str::slug($tag['name']),
                'type' => $tag['type'],
                'icon' => $tag['icon'],
                'is_active' => true,
                'is_featured' => $tag['is_featured'],
                'sort_order' => $tag['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------- PANSİYON TİPLERİ --------------------
        
        $boardTypes = [
            [
                'name' => 'Her Şey Dahil', 
                'code' => 'AI', 
                'description' => 'Konaklama süresince tüm yiyecek, içecek ve belirli aktivitelerin dahil olduğu, ekstra ücret gerektirmeyen konsept',
                'includes' => ['Tüm öğünler', 'Yerli alkollü ve alkolsüz içecekler', 'Snack servis', 'Belirli aktiviteler'],
                'excludes' => ['İthal içecekler', 'Özel aktiviteler', 'Spa hizmetleri'],
                'sort_order' => 1
            ],
            [
                'name' => 'Ultra Her Şey Dahil', 
                'code' => 'UAI', 
                'description' => 'Her şey dahil konseptini daha geniş yiyecek, içecek ve aktivite seçenekleriyle sunan premium konsept',
                'includes' => ['Tüm öğünler', 'Yerli ve ithal içecekler', 'Snack servis', 'A\'la Carte restoranlar', 'Çoğu aktivite'],
                'excludes' => ['Özel masajlar', 'Bazı ekstra hizmetler'],
                'sort_order' => 2
            ],
            [
                'name' => 'Tam Pansiyon', 
                'code' => 'FB', 
                'description' => 'Konaklama fiyatına sabah kahvaltısı, öğle ve akşam yemeğinin dahil olduğu, içeceklerin genelde ekstra ücretlendirildiği konsept',
                'includes' => ['Kahvaltı', 'Öğle yemeği', 'Akşam yemeği'],
                'excludes' => ['İçecekler', 'Atıştırmalıklar', 'Aktiviteler'],
                'sort_order' => 3
            ],
            [
                'name' => 'Tam Pansiyon Plus', 
                'code' => 'FB+', 
                'description' => 'Tam pansiyon konseptine ek olarak yemeklerde belirli içeceklerin dahil olduğu konsept',
                'includes' => ['Kahvaltı', 'Öğle yemeği', 'Akşam yemeği', 'Yemekler sırasında sınırlı içecekler'],
                'excludes' => ['Yemek dışında içecekler', 'Atıştırmalıklar', 'Aktiviteler'],
                'sort_order' => 4
            ],
            [
                'name' => 'Yarım Pansiyon', 
                'code' => 'HB', 
                'description' => 'Konaklama fiyatına sabah kahvaltısı ve akşam yemeğinin dahil olduğu konsept',
                'includes' => ['Kahvaltı', 'Akşam yemeği'],
                'excludes' => ['Öğle yemeği', 'İçecekler', 'Aktiviteler'],
                'sort_order' => 5
            ],
            [
                'name' => 'Oda & Kahvaltı', 
                'code' => 'BB', 
                'description' => 'Konaklama fiyatına sadece sabah kahvaltısının dahil olduğu konsept',
                'includes' => ['Kahvaltı'],
                'excludes' => ['Öğle yemeği', 'Akşam yemeği', 'İçecekler', 'Aktiviteler'],
                'sort_order' => 6
            ],
            [
                'name' => 'Sadece Oda', 
                'code' => 'RO', 
                'description' => 'Herhangi bir yiyecek veya içecek servisinin dahil olmadığı, yalnızca konaklama hizmetini içeren konsept',
                'includes' => ['Sadece oda'],
                'excludes' => ['Tüm öğünler', 'İçecekler', 'Aktiviteler'],
                'sort_order' => 7
            ],
        ];

        foreach ($boardTypes as $type) {
            DB::table('board_types')->insertGetId([
                'name' => $type['name'],
                'code' => $type['code'],
                'description' => $type['description'],
                'icon' => 'meal',
                'includes' => json_encode($type['includes']),
                'excludes' => json_encode($type['excludes']),
                'sort_order' => $type['sort_order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------- ODA TİPLERİ --------------------
        
        $roomTypes = [
            [
                'name' => 'Standart Oda', 
                'description' => 'Otelin temel oda tipi, bir veya iki kişilik yataklı, temel konfor ve olanaklara sahip odalar',
                'base_capacity' => 2,
                'max_capacity' => 3,
                'max_adults' => 2,
                'max_children' => 1,
                'size' => 25,
                'features' => ['Duş/Tuvalet', 'LCD TV', 'Telefon', 'Mini bar', 'Klima', 'Saç kurutma makinesi'],
                'base_price' => 1000,
                'is_featured' => false,
                'sort_order' => 1
            ],
            [
                'name' => 'Superior Oda', 
                'description' => 'Standart odalardan daha geniş, genellikle daha iyi bir manzaraya sahip, bazı ek olanaklarla donatılmış odalar',
                'base_capacity' => 2,
                'max_capacity' => 3,
                'max_adults' => 2,
                'max_children' => 1,
                'size' => 30,
                'features' => ['Duş/Tuvalet', 'LCD TV', 'Telefon', 'Mini bar', 'Klima', 'Saç kurutma makinesi', 'Oturma alanı', 'Balkon'],
                'base_price' => 1300,
                'is_featured' => false,
                'sort_order' => 2
            ],
            [
                'name' => 'Deluxe Oda', 
                'description' => 'Lüks ve konforun ön planda olduğu, kaliteli mobilyalar ve ekstra olanaklarla donatılmış, geniş ve ferah odalar',
                'base_capacity' => 2,
                'max_capacity' => 4,
                'max_adults' => 2,
                'max_children' => 2,
                'size' => 35,
                'features' => ['Banyo/Tuvalet', 'LED TV', 'Telefon', 'Mini bar', 'Klima', 'Saç kurutma makinesi', 'Oturma alanı', 'Balkon', 'Ücretsiz WiFi', 'Kasa'],
                'base_price' => 1600,
                'is_featured' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Aile Odası', 
                'description' => 'Aileler için tasarlanmış, genellikle ara kapılı veya geniş, birden fazla yatak odasına sahip odalar',
                'base_capacity' => 2,
                'max_capacity' => 5,
                'max_adults' => 3,
                'max_children' => 2,
                'size' => 45,
                'features' => ['İki ayrı yatak odası', 'İki banyo', 'LED TV', 'Telefon', 'Mini bar', 'Klima', 'Saç kurutma makinesi', 'Oturma alanı', 'Balkon'],
                'base_price' => 2000,
                'is_featured' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Süit', 
                'description' => 'Ayrı yatak odası ve oturma odasına sahip, lüks mobilyalarla döşenmiş, üst düzey konforlu odalar',
                'base_capacity' => 2,
                'max_capacity' => 4,
                'max_adults' => 2,
                'max_children' => 2,
                'size' => 60,
                'features' => ['Ayrı yatak odası', 'Oturma odası', 'Jakuzili banyo', 'LED TV', 'Ücretsiz mini bar', 'Espresso makinesi', 'Klima', 'Saç kurutma makinesi', 'Balkon veya teras'],
                'base_price' => 3000,
                'is_featured' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Villa', 
                'description' => 'Otel kompleksi içinde ayrı bir yapı olarak konumlanmış, tam donanımlı, özel olanaklara sahip lüks konaklama birimleri',
                'base_capacity' => 4,
                'max_capacity' => 8,
                'max_adults' => 6,
                'max_children' => 2,
                'size' => 120,
                'features' => ['Birden fazla yatak odası', 'Tam donanımlı mutfak', 'Oturma odası', 'Özel havuz', 'Bahçe', 'LED TV', 'Ses sistemi', 'Tam donanımlı banyo', 'Özel hizmetler'],
                'base_price' => 5000,
                'is_featured' => true,
                'sort_order' => 6
            ],
        ];

        foreach ($roomTypes as $type) {
            $roomTypeId = DB::table('room_types')->insertGetId([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'description' => $type['description'],
                'base_capacity' => $type['base_capacity'],
                'max_capacity' => $type['max_capacity'], 
                'max_adults' => $type['max_adults'],
                'max_children' => $type['max_children'],
                'size' => (string)$type['size'],
                'features' => json_encode($type['features']),
                'is_active' => true,
                'sort_order' => $type['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Oda tipi-pansiyon tipi ilişkisi
            $boardTypeIds = DB::table('board_types')->pluck('id');
            foreach ($boardTypeIds as $boardTypeId) {
                DB::table('room_type_board_type')->insert([
                    'room_type_id' => $roomTypeId,
                    'board_type_id' => $boardTypeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // -------------------- ODA ÖZELLİKLERİ --------------------
        
        $roomAmenities = [
            [
                'name' => 'Klima', 
                'category' => 'comfort', 
                'icon' => 'air-conditioner', 
                'description' => 'Isıtma ve soğutma özelliğine sahip klima sistemi',
                'sort_order' => 1
            ],
            [
                'name' => 'Mini Bar', 
                'category' => 'food_beverage', 
                'icon' => 'refrigerator', 
                'description' => 'İçecekler ve atıştırmalıklarla dolu küçük buzdolabı',
                'sort_order' => 2
            ],
            [
                'name' => 'Ücretsiz Wi-Fi', 
                'category' => 'connectivity', 
                'icon' => 'wifi', 
                'description' => 'Yüksek hızlı kablosuz internet erişimi',
                'sort_order' => 3
            ],
            [
                'name' => 'LED TV', 
                'category' => 'entertainment', 
                'icon' => 'tv', 
                'description' => 'Geniş ekran LED televizyon ve uluslararası kanallar',
                'sort_order' => 4
            ],
            [
                'name' => 'Balkon', 
                'category' => 'outdoor', 
                'icon' => 'balcony', 
                'description' => 'Manzaralı oturma alanlı balkon',
                'sort_order' => 5
            ],
            [
                'name' => 'Kasa', 
                'category' => 'security', 
                'icon' => 'safe', 
                'description' => 'Değerli eşyalarınız için elektronik kasa',
                'sort_order' => 6
            ],
            [
                'name' => 'Çay/Kahve Makinesi', 
                'category' => 'food_beverage', 
                'icon' => 'coffee', 
                'description' => 'Ücretsiz çay ve kahve yapma imkanı',
                'sort_order' => 7
            ],
            [
                'name' => 'Deniz Manzarası', 
                'category' => 'view', 
                'icon' => 'ocean-view', 
                'description' => 'Odadan deniz manzarası',
                'sort_order' => 8
            ],
            [
                'name' => 'Jakuzi', 
                'category' => 'bathroom', 
                'icon' => 'hot-tub', 
                'description' => 'Özel jakuzili banyo',
                'sort_order' => 9
            ],
            [
                'name' => 'Oturma Alanı', 
                'category' => 'furniture', 
                'icon' => 'sofa', 
                'description' => 'Konforlu koltuk veya kanepeli oturma alanı',
                'sort_order' => 10
            ],
            [
                'name' => 'Ütü Seti', 
                'category' => 'comfort', 
                'icon' => 'iron', 
                'description' => 'Ütü ve ütü masası',
                'sort_order' => 11
            ],
            [
                'name' => 'Saç Kurutma Makinesi', 
                'category' => 'bathroom', 
                'icon' => 'hair-dryer', 
                'description' => 'Profesyonel saç kurutma makinesi',
                'sort_order' => 12
            ],
        ];

        foreach ($roomAmenities as $amenity) {
            DB::table('room_amenities')->insertGetId([
                'name' => $amenity['name'],
                'slug' => Str::slug($amenity['name']),
                'category' => $amenity['category'],
                'icon' => $amenity['icon'],
                'description' => $amenity['description'],
                'is_active' => true,
                'sort_order' => $amenity['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // -------------------- OTEL TESİS ÖZELLİKLERİ --------------------
        
        $hotelAmenities = [
            [
                'name' => 'Açık Havuz', 
                'category' => 'pool', 
                'icon' => 'pool',
                'description' => 'Açık yüzme havuzu',
                'sort_order' => 1
            ],
            [
                'name' => 'Kapalı Havuz', 
                'category' => 'pool', 
                'icon' => 'indoor-pool',
                'description' => 'Isıtmalı kapalı yüzme havuzu',
                'sort_order' => 2
            ],
            [
                'name' => 'Spa Merkezi', 
                'category' => 'wellness', 
                'icon' => 'spa',
                'description' => 'Masaj, hamam, sauna ve güzellik hizmetleri sunan spa merkezi',
                'sort_order' => 3
            ],
            [
                'name' => 'Fitness Merkezi', 
                'category' => 'fitness', 
                'icon' => 'dumbbell',
                'description' => 'Modern ekipmanlarla donatılmış spor salonu',
                'sort_order' => 4
            ],
            [
                'name' => 'Restoran', 
                'category' => 'dining', 
                'icon' => 'restaurant',
                'description' => 'Açık büfe ve à la carte hizmet veren restoran',
                'sort_order' => 5
            ],
            [
                'name' => 'Bar', 
                'category' => 'dining', 
                'icon' => 'cocktail',
                'description' => 'Geniş içecek menüsüyle hizmet veren bar',
                'sort_order' => 6
            ],
            [
                'name' => 'Çocuk Kulübü', 
                'category' => 'kids', 
                'icon' => 'kids-club',
                'description' => 'Çeşitli yaş gruplarına özel aktivitelerle dolu çocuk kulübü',
                'sort_order' => 7
            ],
            [
                'name' => 'Plaj', 
                'category' => 'beach', 
                'icon' => 'beach',
                'description' => 'Özel plaj alanı',
                'sort_order' => 8
            ],
            [
                'name' => 'Toplantı Odaları', 
                'category' => 'business', 
                'icon' => 'meeting-room',
                'description' => 'Tam donanımlı toplantı ve konferans salonları',
                'sort_order' => 9
            ],
            [
                'name' => 'Su Sporları Merkezi', 
                'category' => 'activities', 
                'icon' => 'water-sports',
                'description' => 'Çeşitli su sporları aktiviteleri',
                'sort_order' => 10
            ],
            [
                'name' => 'Animasyon', 
                'category' => 'entertainment', 
                'icon' => 'entertainment',
                'description' => 'Gündüz ve gece animasyon programları',
                'sort_order' => 11
            ],
            [
                'name' => 'Ücretsiz Otopark', 
                'category' => 'parking', 
                'icon' => 'parking',
                'description' => 'Misafirler için ücretsiz otopark alanı',
                'sort_order' => 12
            ],
            [
                'name' => 'Concierge Servisi', 
                'category' => 'service', 
                'icon' => 'concierge',
                'description' => 'Özel istek ve organizasyonlar için concierge hizmeti',
                'sort_order' => 13
            ],
            [
                'name' => 'İş Merkezi', 
                'category' => 'business', 
                'icon' => 'business-center',
                'description' => 'Bilgisayar, yazıcı ve internet erişimi ile tam donanımlı iş merkezi',
                'sort_order' => 14
            ],
            [
                'name' => 'Tenis Kortu', 
                'category' => 'sports', 
                'icon' => 'tennis',
                'description' => 'Profesyonel tenis kortları',
                'sort_order' => 15
            ],
        ];

        foreach ($hotelAmenities as $amenity) {
            $amenityId = DB::table('hotel_amenities')->insertGetId([
                'name' => $amenity['name'],
                'slug' => Str::slug($amenity['name']),
                'category' => $amenity['category'],
                'icon' => $amenity['icon'],
                'description' => $amenity['description'],
                'is_active' => true,
                'sort_order' => $amenity['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // -------------------- OTELLER --------------------
        
        $hotels = [
            [
                'name' => 'Lara Barut Collection',
                'region' => 'Lara',
                'type' => 'Resort',
                'description' => 'Lara Plajı\'nda yer alan ultra lüks bir resort. Geniş havuzları, özel plajı, dünya mutfaklarından örnekler sunan restoranları ve birinci sınıf spa merkezi ile misafirlerine unutulmaz bir tatil deneyimi sunuyor.',
                'short_description' => 'Antalya\'nın en lüks resortlarından biri, Ultra Her Şey Dahil konsepti ve seçkin hizmetleriyle öne çıkıyor.',
                'stars' => 5,
                'tags' => ['Denize Sıfır', 'Ultra Lüks', 'Spa', 'Aile Dostu'],
                'features' => ['Açık Havuz', 'Kapalı Havuz', 'Spa Merkezi', 'Restoran', 'Bar', 'Plaj', 'Animasyon', 'Çocuk Kulübü'],
                'latitude' => 36.8552,
                'longitude' => 30.7904
            ],
            [
                'name' => 'Voyage Belek Golf & Spa',
                'region' => 'Belek',
                'type' => 'Resort',
                'description' => 'Belek\'in eşsiz plajında konumlanan, golf sahalarına yakın lüks bir resort. Su kaydırakları, aktivite havuzları, spa merkezi ve çeşitli spor olanaklarıyla aktif bir tatil için ideal bir seçenek.',
                'short_description' => 'Golf tutkunları ve aileler için ultra her şey dahil konseptiyle hizmet veren lüks bir tesis.',
                'stars' => 5,
                'tags' => ['Denize Sıfır', 'Golf', 'Spa', 'Aile Dostu'],
                'features' => ['Açık Havuz', 'Kapalı Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Çocuk Kulübü', 'Tenis Kortu'],
                'latitude' => 36.8635,
                'longitude' => 31.0519
            ],
            [
                'name' => 'Rixos Downtown Antalya',
                'region' => 'Antalya',
                'type' => 'Şehir Oteli',
                'description' => 'Antalya şehir merkezinde, Konyaaltı Plajı\'na yakın konumda yer alan lüks bir şehir oteli. Hem iş hem de tatil amaçlı konaklamalar için ideal olan otel, kaliteli hizmet anlayışı ve modern tasarımıyla öne çıkıyor.',
                'short_description' => 'Antalya şehir merkezinde, plaja yakın konumlanmış, şehir otelciliğinde beş yıldızlı konfor sunan bir tesis.',
                'stars' => 5,
                'tags' => ['Merkezde', 'Spa', 'Toplantı Odaları'],
                'features' => ['Açık Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Toplantı Odaları', 'Concierge Servisi'],
                'latitude' => 36.8852,
                'longitude' => 30.6946
            ],
            [
                'name' => 'Bodrum Rixos Premium',
                'region' => 'Bodrum',
                'type' => 'Resort',
                'description' => 'Bodrum Yarımadası\'nın eşsiz manzarasına sahip bu lüks resort, özel plajı, dünya standartlarındaki spa merkezi ve gurme restoranlarıyla unutulmaz bir tatil deneyimi sunuyor.',
                'short_description' => 'Bodrum\'un en lüks resortlarından biri, denize sıfır konumu ve ultra her şey dahil konseptiyle misafirlerini ağırlıyor.',
                'stars' => 5,
                'tags' => ['Denize Sıfır', 'Ultra Lüks', 'Spa', 'Romantik'],
                'features' => ['Açık Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Plaj', 'Su Sporları Merkezi'],
                'latitude' => 37.0382,
                'longitude' => 27.4241
            ],
            [
                'name' => 'D-Resort Grand Azur Marmaris',
                'region' => 'Marmaris',
                'type' => 'Resort',
                'description' => 'Marmaris\'in berrak sularına sıfır konumda yer alan bu zarif resort, geniş bahçeleri, özel plajı ve lüks spa merkezi ile konuklarına huzurlu bir tatil vadediyor.',
                'short_description' => 'Marmaris\'in en güzel koylarından birinde, her şey dahil konsept ve zarif tasarımla hizmet veren bir otel.',
                'stars' => 5,
                'tags' => ['Denize Sıfır', 'Spa', 'Aile Dostu'],
                'features' => ['Açık Havuz', 'Kapalı Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Plaj'],
                'latitude' => 36.8425,
                'longitude' => 28.2560
            ],
            [
                'name' => 'Four Seasons Hotel Istanbul at the Bosphorus',
                'region' => 'Beşiktaş',
                'type' => 'Şehir Oteli',
                'description' => 'Boğaz kıyısında, tarihi bir Osmanlı sarayından dönüştürülen bu lüks otel, İstanbul\'un en prestijli konaklama seçeneklerinden biri. Eşsiz Boğaz manzarası, dünya standartlarında hizmet anlayışı ve seçkin restoranlarıyla fark yaratıyor.',
                'short_description' => 'Boğaz\'ın kıyısında konumlanan, tarihi bir yapıda hizmet veren, İstanbul\'un en lüks otellerinden biri.',
                'stars' => 5,
                'tags' => ['Ultra Lüks', 'Spa', 'Romantik'],
                'features' => ['Açık Havuz', 'Kapalı Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Concierge Servisi'],
                'latitude' => 41.0410,
                'longitude' => 29.0520
            ],
            [
                'name' => 'Hilton Istanbul Bomonti',
                'region' => 'Şişli',
                'type' => 'Şehir Oteli',
                'description' => 'İstanbul\'un en yüksek binalarından birinde yer alan bu modern şehir oteli, panoramik şehir manzarası, geniş toplantı salonları ve dünya markası güvencesiyle iş ve tatil amaçlı konaklamalarda tercih ediliyor.',
                'short_description' => 'İstanbul\'un merkezinde, modern mimarisiyle öne çıkan, iş ve tatil amaçlı konaklamalar için ideal bir seçenek.',
                'stars' => 5,
                'tags' => ['Merkezde', 'Toplantı Odaları', 'Spa'],
                'features' => ['Kapalı Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Toplantı Odaları', 'İş Merkezi'],
                'latitude' => 41.0563,
                'longitude' => 28.9773
            ],
            [
                'name' => 'JW Marriott Istanbul Bosphorus',
                'region' => 'Beyoğlu',
                'type' => 'Şehir Oteli',
                'description' => 'Tarihi Karaköy bölgesinde, eski bir İtalyan hastanesinden dönüştürülen bu lüks otel, boğaz manzaralı odaları, çatı restoranı ve dünya standartlarındaki hizmet anlayışıyla konuklarını ağırlıyor.',
                'short_description' => 'Tarihi bir yapıda modern lüksü yaşatan, boğaz manzaralı butik bir şehir oteli.',
                'stars' => 5,
                'tags' => ['Merkezde', 'Ultra Lüks', 'Spa'],
                'features' => ['Açık Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Toplantı Odaları', 'Concierge Servisi'],
                'latitude' => 41.0275,
                'longitude' => 28.9802
            ],
            [
                'name' => 'Merit Royal Otel & Casino',
                'region' => 'İzmir',
                'type' => 'Şehir Oteli',
                'description' => 'İzmir şehir merkezinde yer alan bu lüks otel, modern tasarımı, kaliteli hizmet anlayışı ve kapsamlı olanakları ile iş ve tatil amaçlı konaklamalar için ideal bir seçenek sunuyor.',
                'short_description' => 'İzmir\'in merkezinde, modern mimarisi ve lüks olanaklarıyla öne çıkan şehir oteli.',
                'stars' => 5,
                'tags' => ['Merkezde', 'Toplantı Odaları', 'Spa'],
                'features' => ['Açık Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Toplantı Odaları', 'İş Merkezi'],
                'latitude' => 38.4219,
                'longitude' => 27.1396
            ],
            [
                'name' => 'Liberty Hotels Lykia',
                'region' => 'Fethiye',
                'type' => 'Resort',
                'description' => 'Fethiye\'nin eşsiz Ölüdeniz manzarasına hakim tepede konumlanan bu lüks resort, 19 havuzu, özel plajı ve kapsamlı her şey dahil konsepti ile aileler için mükemmel bir tatil vadediyor.',
                'short_description' => 'Ölüdeniz manzaralı tepede konumlanan, havuz kompleksi ve kapsamlı aktivite programı ile öne çıkan resort.',
                'stars' => 5,
                'tags' => ['Denize Sıfır', 'Aile Dostu', 'Spa'],
                'features' => ['Açık Havuz', 'Spa Merkezi', 'Fitness Merkezi', 'Restoran', 'Bar', 'Plaj', 'Çocuk Kulübü', 'Animasyon'],
                'latitude' => 36.5496,
                'longitude' => 29.1260
            ],
        ];

        // Otelleri veritabanına ekle ve odalara bağla
        foreach ($hotels as $hotel) {
            // İlgili ilçeyi bul
            $regionId = DB::table('regions')->where('name', $hotel['region'])->value('id');
            if (!$regionId) {
                // Bölge bulunamadıysa herhangi bir şehirden bilgi al
                $regionId = DB::table('regions')->where('type', 'city')->inRandomOrder()->value('id');
            }
            
            // Otel tipini bul
            $typeId = DB::table('hotel_types')->where('name', $hotel['type'])->value('id');
            
            // Oteli ekle
            $hotelId = DB::table('hotels')->insertGetId([
                'name' => $hotel['name'],
                'slug' => Str::slug($hotel['name']),
                'region_id' => $regionId,
                'type_id' => $typeId,
                'description' => $hotel['description'],
                'short_description' => $hotel['short_description'],
                'email' => 'info@' . Str::slug($hotel['name']) . '.com',
                'phone' => '+90 ' . rand(500, 599) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'website' => 'https://www.' . Str::slug($hotel['name']) . '.com',
                'latitude' => $hotel['latitude'],
                'longitude' => $hotel['longitude'],
                'star_rating' => $hotel['stars'],
                'avg_rating' => rand(40, 50) / 10, // 4.0 - 5.0 arası
                'is_active' => true,
                'is_featured' => $hotel['stars'] >= 5,
                'sort_order' => array_search($hotel, $hotels) + 1,
                'check_in_out' => json_encode([
                    'check_in_from' => '14:00',
                    'check_in_until' => '23:59',
                    'check_out_from' => '07:00',
                    'check_out_until' => '12:00'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
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
            
            // Otel özelliklerini ekle
            foreach ($hotel['features'] as $amenityName) {
                $amenityId = DB::table('hotel_amenities')->where('name', $amenityName)->value('id');
                if ($amenityId) {
                    DB::table('hotel_hotel_amenity')->insert([
                        'hotel_id' => $hotelId,
                        'hotel_amenity_id' => $amenityId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // Otel iletişim bilgisi ekle
            DB::table('hotel_contacts')->insert([
                'hotel_id' => $hotelId,
                'name' => 'Otel Müdürü',
                'position' => 'Genel Müdür',
                'department' => 'Yönetim',
                'email' => 'manager@' . Str::slug($hotel['name']) . '.com',
                'phone' => '+90 ' . rand(500, 599) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'is_primary' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Satış müdürü ekle
            DB::table('hotel_contacts')->insert([
                'hotel_id' => $hotelId,
                'name' => 'Satış Müdürü',
                'position' => 'Satış Müdürü',
                'department' => 'Satış & Pazarlama',
                'email' => 'sales@' . Str::slug($hotel['name']) . '.com',
                'phone' => '+90 ' . rand(500, 599) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                'is_primary' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Her otel için tüm oda tiplerinde odalar ekle
            $roomTypeIds = DB::table('room_types')->orderBy('sort_order')->pluck('id');
            
            // Oda amenity IDs
            $roomAmenityIds = DB::table('room_amenities')->pluck('id')->toArray();
            
            // Board types
            $boardTypeIds = DB::table('board_types')->pluck('id');
            
            foreach ($roomTypeIds as $index => $roomTypeId) {
                // Her oda tipinden 2-5 oda oluştur
                $roomCount = rand(2, 5);
                $roomType = DB::table('room_types')->where('id', $roomTypeId)->first();
                
                // Üst düzey otellerde daha az villa/suite, alt seviye otellerde daha az lüks oda
                if (($roomType->name == 'Villa' || $roomType->name == 'Süit') && $hotel['stars'] < 5) {
                    $roomCount = rand(0, 2); // 5 yıldızdan az otellerde az sayıda veya hiç villa/süit olmasın
                }
                
                if ($roomType->name == 'Standart Oda' && $hotel['stars'] >= 5) {
                    $roomCount = rand(5, 10); // Lüks otellerde daha fazla standart oda
                }
                
                // Lüks resort otellerde daha fazla aile odası
                if ($roomType->name == 'Aile Odası' && $hotel['type'] == 'Resort' && $hotel['stars'] >= 5) {
                    $roomCount = rand(4, 8);
                }
                
                // Şehir otellerinde villa olmasın
                if ($roomType->name == 'Villa' && $hotel['type'] == 'Şehir Oteli') {
                    $roomCount = 0;
                }
                
                for ($i = 1; $i <= $roomCount; $i++) {
                    $floor = rand(1, 5);
                    $roomNumber = $floor . str_pad($i + ($index * 10), 2, '0', STR_PAD_LEFT);
                    
                    // Oda adını oda tipine göre oluştur
                    $roomName = $roomType->name;
                    if ($roomType->name == 'Standart Oda' || $roomType->name == 'Superior Oda' || $roomType->name == 'Deluxe Oda') {
                        $views = ['Bahçe Manzaralı', 'Deniz Manzaralı', 'Havuz Manzaralı', 'Kara Manzaralı'];
                        if (in_array('Denize Sıfır', $hotel['tags'])) {
                            // Denize sıfır otellerde deniz manzaralı oda olasılığı daha yüksek
                            $views = ['Deniz Manzaralı', 'Deniz Manzaralı', 'Bahçe Manzaralı', 'Havuz Manzaralı'];
                        }
                        $roomName .= ' - ' . $views[array_rand($views)];
                    }
                    
                    // Fiyatı oda tipine ve otel yıldızına göre ayarla
                    $basePrice = $roomType->base_price;
                    $priceMultiplier = ($hotel['stars'] - 3) * 0.2 + 1; // 5 yıldız için 1.4, 4 yıldız için 1.2
                    $price = $basePrice * $priceMultiplier;
                    
                    // Resort otellerde fiyatı biraz daha arttır
                    if ($hotel['type'] == 'Resort') {
                        $price *= 1.1;
                    }
                    
                    $roomId = DB::table('rooms')->insertGetId([
                        'hotel_id' => $hotelId,
                        'room_type_id' => $roomTypeId,
                        'name' => $roomName,
                        'room_number' => $roomNumber,
                        'floor' => $floor,
                        'description' => $roomType->name . ' açıklaması',
                        'max_adults' => $roomType->max_adults,
                        'max_children' => $roomType->max_children,
                        'max_occupancy' => $roomType->max_adults + $roomType->max_children,
                        'size' => $roomType->size,
                        'price' => $price,
                        'features' => $roomType->features,
                        'is_active' => true,
                        'is_available' => true,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Odalar için özellikler ekle (amenities)
                    shuffle($roomAmenityIds);
                    $selectedAmenityCount = min(count($roomAmenityIds), 5 + $index); // Daha lüks odalarda daha fazla özellik
                    for ($j = 0; $j < $selectedAmenityCount; $j++) {
                        DB::table('room_room_amenity')->insert([
                            'room_id' => $roomId,
                            'room_amenity_id' => $roomAmenityIds[$j],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    // Oda için pansiyon tiplerini belirle
                    $selectedBoardTypes = $boardTypeIds;
                    if ($hotel['type'] == 'Şehir Oteli') {
                        // Şehir otellerinde genelde her şey dahil olmaz
                        $selectedBoardTypes = DB::table('board_types')
                            ->whereNotIn('code', ['AI', 'UAI'])
                            ->pluck('id');
                    }
                    
                    foreach ($selectedBoardTypes as $boardTypeId) {
                        DB::table('room_board_type')->insert([
                            'room_id' => $roomId,
                            'board_type_id' => $boardTypeId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    
                    // Rate Plan oluştur
                    $boardTypeIds = DB::table('board_types')->pluck('id');
                    foreach ($boardTypeIds as $boardTypeId) {
                        $boardType = DB::table('board_types')->where('id', $boardTypeId)->first();
                        
                        // Şehir otelleri için her şey dahil rate plan oluşturma
                        if ($hotel['type'] == 'Şehir Oteli' && in_array($boardType->code, ['AI', 'UAI'])) {
                            continue;
                        }
                        
                        $ratePlanId = DB::table('rate_plans')->insertGetId([
                            'hotel_id' => $hotelId,
                            'room_id' => $roomId,
                            'board_type_id' => $boardTypeId,
                            'is_per_person' => $boardType->code == 'AI' || $boardType->code == 'UAI' || $boardType->code == 'FB+',
                            'status' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}