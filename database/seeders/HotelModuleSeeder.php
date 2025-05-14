<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Plugins\Accommodation\Models\HotelTag;
use App\Models\Hotel;
use App\Models\HotelContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HotelModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regions
        $regions = [
            ['name' => 'Antalya', 'description' => 'Akdeniz\'in incisi, turizm cenneti'],
            ['name' => 'İstanbul', 'description' => 'İki kıtayı birleştiren şehir'],
            ['name' => 'İzmir', 'description' => 'Ege\'nin incisi'],
            ['name' => 'Bodrum', 'description' => 'Tatil cenneti'],
            ['name' => 'Kapadokya', 'description' => 'Doğa harikası'],
            ['name' => 'Trabzon', 'description' => 'Karadeniz\'in incisi'],
        ];

        foreach ($regions as $regionData) {
            Region::updateOrCreate(
                ['slug' => Str::slug($regionData['name'])],
                [
                    'name' => $regionData['name'],
                    'description' => $regionData['description'],
                    'is_active' => true,
                ]
            );
        }

        // Create hotel tags
        $tags = [
            ['name' => 'Deniz Manzaralı', 'type' => 'feature', 'icon' => 'beach'],
            ['name' => 'Havuzlu', 'type' => 'amenity', 'icon' => 'pool'],
            ['name' => 'Spa', 'type' => 'amenity', 'icon' => 'spa'],
            ['name' => 'İş Oteli', 'type' => 'category', 'icon' => 'business'],
            ['name' => 'Plaj Oteli', 'type' => 'category', 'icon' => 'beach'],
            ['name' => 'Aile Dostu', 'type' => 'feature', 'icon' => 'family'],
            ['name' => 'Herşey Dahil', 'type' => 'category', 'icon' => 'all-inclusive'],
            ['name' => 'Ücretsiz Wifi', 'type' => 'amenity', 'icon' => 'wifi'],
            ['name' => 'Otopark', 'type' => 'amenity', 'icon' => 'parking'],
            ['name' => 'Fitness Merkezi', 'type' => 'amenity', 'icon' => 'fitness'],
        ];

        foreach ($tags as $tagData) {
            HotelTag::updateOrCreate(
                ['slug' => Str::slug($tagData['name'])],
                [
                    'name' => $tagData['name'],
                    'type' => $tagData['type'],
                    'icon' => $tagData['icon'],
                    'is_active' => true,
                    'is_featured' => $tagData['type'] === 'feature',
                ]
            );
        }

        // Create sample hotels
        $hotels = [
            [
                'name' => 'Antalya Resort & Spa',
                'region_id' => 1,
                'type_id' => 1,
                'stars' => 5,
                'city' => 'Antalya',
                'country' => 'Türkiye',
                'tags' => [1, 2, 3, 5, 6, 7, 8, 9, 10],
                'amenities' => ['Özel Plaj', 'Açık Havuz', 'Kapalı Havuz', 'Spa', 'Fitness Merkezi', 'Restoran', 'Bar'],
            ],
            [
                'name' => 'İstanbul Park Hotel',
                'region_id' => 2,
                'type_id' => 2,
                'stars' => 4,
                'city' => 'İstanbul',
                'country' => 'Türkiye',
                'tags' => [4, 8, 9],
                'amenities' => ['Restoran', 'Bar', 'Toplantı Odası', 'Concierge'],
            ],
            [
                'name' => 'İzmir Beach Club',
                'region_id' => 3,
                'type_id' => 1,
                'stars' => 4,
                'city' => 'İzmir',
                'country' => 'Türkiye',
                'tags' => [1, 2, 5, 7, 8],
                'amenities' => ['Özel Plaj', 'Açık Havuz', 'Restoran', 'Bar', 'Spor Aktiviteleri'],
            ],
            [
                'name' => 'Bodrum Luxury Suites',
                'region_id' => 4,
                'type_id' => 3,
                'stars' => 5,
                'city' => 'Bodrum',
                'country' => 'Türkiye',
                'tags' => [1, 2, 3, 5, 8, 10],
                'amenities' => ['Özel Plaj', 'Sonsuzluk Havuzu', 'Spa', 'Restoran', 'Bar', 'Butler Servisi'],
            ],
            [
                'name' => 'Kapadokya Cave Rooms',
                'region_id' => 5,
                'type_id' => 3,
                'stars' => 4,
                'city' => 'Nevşehir',
                'country' => 'Türkiye',
                'tags' => [6, 8, 9],
                'amenities' => ['Restoran', 'Teras', 'Ücretsiz Otopark', 'Tur Servisi'],
            ],
            [
                'name' => 'Trabzon Deluxe Hotel',
                'region_id' => 6,
                'type_id' => 2,
                'stars' => 3,
                'city' => 'Trabzon',
                'country' => 'Türkiye',
                'tags' => [4, 8, 9],
                'amenities' => ['Restoran', 'Toplantı Odası', 'Ücretsiz Otopark'],
            ],
        ];

        foreach ($hotels as $hotelData) {
            $tagIds = $hotelData['tags'];
            unset($hotelData['tags']);

            $slug = Str::slug($hotelData['name']);
            $hotel = Hotel::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $hotelData['name'],
                    'region_id' => $hotelData['region_id'],
                    'type_id' => $hotelData['type_id'],
                    'stars' => $hotelData['stars'],
                    'city' => $hotelData['city'],
                    'country' => $hotelData['country'],
                    'amenities' => $hotelData['amenities'],
                    'description' => 'Bu otelle ilgili detaylı açıklama buraya gelecek.',
                    'short_description' => 'Kısa açıklama metni.',
                    'is_active' => true,
                    'is_featured' => $hotelData['stars'] >= 5,
                ]
            );

            // Attach tags
            $hotel->tags()->attach($tagIds);

            // Create sample contacts
            HotelContact::updateOrCreate(
                [
                    'hotel_id' => $hotel->id,
                    'email' => 'gm@' . Str::slug($hotel->name) . '.com',
                ],
                [
                    'name' => 'Genel Müdür',
                    'position' => 'Genel Müdür',
                    'department' => 'Yönetim',
                    'phone' => '+90 555 123 4567',
                    'is_primary' => true,
                    'is_active' => true,
                ]
            );

            HotelContact::updateOrCreate(
                [
                    'hotel_id' => $hotel->id,
                    'email' => 'sales@' . Str::slug($hotel->name) . '.com',
                ],
                [
                    'name' => 'Satış Müdürü',
                    'position' => 'Satış Müdürü',
                    'department' => 'Satış',
                    'phone' => '+90 555 987 6543',
                    'is_active' => true,
                ]
            );
        }
    }
}