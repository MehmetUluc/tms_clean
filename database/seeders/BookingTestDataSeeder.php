<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\HotelType;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Amenities\Models\HotelAmenity;
use App\Plugins\Amenities\Models\RoomAmenity;
use Illuminate\Support\Str;

class BookingTestDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Creating regions, hotels, rooms and pricing data...');
        
        // Hotel Types
        $hotelTypes = [
            ['name' => 'Resort Hotel', 'slug' => 'resort-hotel'],
            ['name' => 'City Hotel', 'slug' => 'city-hotel'],
            ['name' => 'Boutique Hotel', 'slug' => 'boutique-hotel'],
            ['name' => 'Beach Hotel', 'slug' => 'beach-hotel'],
        ];
        
        foreach ($hotelTypes as $type) {
            HotelType::firstOrCreate(['slug' => $type['slug']], $type);
        }
        
        // Room Types
        $roomTypes = [
            ['name' => 'Standard Room', 'slug' => 'standard-room'],
            ['name' => 'Deluxe Room', 'slug' => 'deluxe-room'],
            ['name' => 'Suite', 'slug' => 'suite'],
            ['name' => 'Family Room', 'slug' => 'family-room'],
            ['name' => 'Junior Suite', 'slug' => 'junior-suite'],
        ];
        
        foreach ($roomTypes as $type) {
            RoomType::firstOrCreate(['slug' => $type['slug']], $type);
        }
        
        // Hotel Amenities
        $hotelAmenities = [
            ['name' => 'Swimming Pool', 'slug' => 'swimming-pool', 'icon' => 'heroicon-o-sparkles'],
            ['name' => 'Free WiFi', 'slug' => 'free-wifi', 'icon' => 'heroicon-o-wifi'],
            ['name' => 'Spa & Wellness', 'slug' => 'spa-wellness', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Restaurant', 'slug' => 'restaurant', 'icon' => 'heroicon-o-shopping-bag'],
            ['name' => 'Fitness Center', 'slug' => 'fitness-center', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Beach Access', 'slug' => 'beach-access', 'icon' => 'heroicon-o-sun'],
            ['name' => 'Airport Shuttle', 'slug' => 'airport-shuttle', 'icon' => 'heroicon-o-truck'],
            ['name' => 'Parking', 'slug' => 'parking', 'icon' => 'heroicon-o-truck'],
            ['name' => 'Pet Friendly', 'slug' => 'pet-friendly', 'icon' => 'heroicon-o-heart'],
            ['name' => 'Bar', 'slug' => 'bar', 'icon' => 'heroicon-o-beer'],
        ];
        
        foreach ($hotelAmenities as $amenity) {
            HotelAmenity::firstOrCreate(['slug' => $amenity['slug']], $amenity);
        }
        
        // Room Amenities
        $roomAmenities = [
            ['name' => 'Air Conditioning', 'slug' => 'air-conditioning'],
            ['name' => 'Mini Bar', 'slug' => 'mini-bar'],
            ['name' => 'Safe Box', 'slug' => 'safe-box'],
            ['name' => 'Hair Dryer', 'slug' => 'hair-dryer'],
            ['name' => 'Balcony', 'slug' => 'balcony'],
            ['name' => 'Sea View', 'slug' => 'sea-view'],
            ['name' => 'Coffee Machine', 'slug' => 'coffee-machine'],
            ['name' => 'Flat Screen TV', 'slug' => 'flat-screen-tv'],
        ];
        
        foreach ($roomAmenities as $amenity) {
            RoomAmenity::firstOrCreate(['slug' => $amenity['slug']], $amenity);
        }
        
        // Turkey as parent
        $turkey = Region::firstOrCreate(
            ['slug' => 'turkey'],
            [
                'name' => 'Turkey',
                'type' => 'country',
                'parent_id' => null,
                'is_active' => true
            ]
        );
        
        // Regions with Hotels
        $regionsData = [
            [
                'name' => 'Antalya',
                'type' => 'city',
                'hotels' => [
                    [
                        'name' => 'Rixos Premium Belek',
                        'star_rating' => 5,
                        'type' => 'beach-hotel',
                        'description' => 'Luxury all-inclusive resort on the Mediterranean coast',
                        'address' => 'İskele Mevkii, Belek',
                        'child_policy' => [
                            'max_children_per_room' => 2,
                            'child_age_limit' => 12,
                            'child_policies' => [
                                ['age_from' => 0, 'age_to' => 2, 'price' => 0],
                                ['age_from' => 3, 'age_to' => 6, 'price' => 50],
                                ['age_from' => 7, 'age_to' => 12, 'price' => 100],
                            ],
                            'child_policy_description' => 'Children up to 2 years stay free. 50% discount for ages 3-6.'
                        ],
                        'refund_type' => 'refundable',
                        'cancellation_deadline' => 48,
                        'amenities' => ['Swimming Pool', 'Beach Access', 'Spa & Wellness', 'Restaurant', 'Bar', 'Free WiFi'],
                        'rooms' => [
                            [
                                'name' => 'Deluxe Sea View Room',
                                'type' => 'deluxe-room',
                                'capacity_adults' => 2,
                                'capacity_children' => 1,
                                'size' => 35,
                                'base_price' => 1500,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Balcony', 'Sea View'],
                                'board_types' => ['AI']
                            ],
                            [
                                'name' => 'Family Suite',
                                'type' => 'family-room',
                                'capacity_adults' => 2,
                                'capacity_children' => 2,
                                'size' => 65,
                                'base_price' => 2500,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Balcony', 'Safe Box'],
                                'board_types' => ['AI', 'FB']
                            ]
                        ]
                    ],
                    [
                        'name' => 'Delphin Imperial',
                        'star_rating' => 5,
                        'type' => 'resort-hotel',
                        'description' => 'Family-friendly resort with extensive facilities',
                        'address' => 'Lara Kundu, Antalya',
                        'child_policy' => [
                            'max_children_per_room' => 3,
                            'child_age_limit' => 14,
                            'child_policies' => [
                                ['age_from' => 0, 'age_to' => 6, 'price' => 0],
                                ['age_from' => 7, 'age_to' => 14, 'price' => 150],
                            ],
                            'child_policy_description' => 'Children up to 6 years stay free in existing beds.'
                        ],
                        'refund_type' => 'both',
                        'cancellation_deadline' => 72,
                        'amenities' => ['Swimming Pool', 'Fitness Center', 'Spa & Wellness', 'Restaurant', 'Bar'],
                        'rooms' => [
                            [
                                'name' => 'Standard Land View',
                                'type' => 'standard-room',
                                'capacity_adults' => 3,
                                'capacity_children' => 1,
                                'size' => 30,
                                'base_price' => 1200,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Safe Box'],
                                'board_types' => ['AI']
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Istanbul',
                'type' => 'city',
                'hotels' => [
                    [
                        'name' => 'Four Seasons Sultanahmet',
                        'star_rating' => 5,
                        'type' => 'boutique-hotel',
                        'description' => 'Luxury hotel in the heart of old Istanbul',
                        'address' => 'Tevkifhane Sok. No:1 Sultanahmet',
                        'child_policy' => [
                            'max_children_per_room' => 1,
                            'child_age_limit' => 18,
                            'child_policies' => [
                                ['age_from' => 0, 'age_to' => 18, 'price' => 0],
                            ],
                            'child_policy_description' => 'One child stays free when using existing bedding.'
                        ],
                        'refund_type' => 'refundable',
                        'cancellation_deadline' => 24,
                        'amenities' => ['Restaurant', 'Bar', 'Free WiFi', 'Spa & Wellness'],
                        'rooms' => [
                            [
                                'name' => 'Premier Room',
                                'type' => 'deluxe-room',
                                'capacity_adults' => 2,
                                'capacity_children' => 1,
                                'size' => 37,
                                'base_price' => 3500,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Safe Box', 'Coffee Machine'],
                                'board_types' => ['BB', 'RO']
                            ],
                            [
                                'name' => 'Deluxe Suite',
                                'type' => 'suite',
                                'capacity_adults' => 2,
                                'capacity_children' => 1,
                                'size' => 65,
                                'base_price' => 5500,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Safe Box', 'Coffee Machine', 'Balcony'],
                                'board_types' => ['BB']
                            ]
                        ]
                    ],
                    [
                        'name' => 'The Ritz-Carlton Istanbul',
                        'star_rating' => 5,
                        'type' => 'city-hotel',
                        'description' => 'Modern luxury with Bosphorus views',
                        'address' => 'Suzer Plaza, Elmadag, Sisli',
                        'child_policy' => [
                            'max_children_per_room' => 2,
                            'child_age_limit' => 12,
                            'child_policies' => [
                                ['age_from' => 0, 'age_to' => 12, 'price' => 0],
                            ],
                            'child_policy_description' => 'Children under 12 stay free in parent\'s room.'
                        ],
                        'refund_type' => 'non_refundable',
                        'non_refundable_discount' => 15,
                        'amenities' => ['Swimming Pool', 'Fitness Center', 'Restaurant', 'Bar', 'Parking'],
                        'rooms' => [
                            [
                                'name' => 'Deluxe Room City View',
                                'type' => 'deluxe-room',
                                'capacity_adults' => 2,
                                'capacity_children' => 1,
                                'size' => 40,
                                'base_price' => 2800,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Safe Box', 'Flat Screen TV'],
                                'board_types' => ['BB', 'RO']
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Bodrum',
                'type' => 'city',
                'hotels' => [
                    [
                        'name' => 'Mandarin Oriental Bodrum',
                        'star_rating' => 5,
                        'type' => 'beach-hotel',
                        'description' => 'Exclusive resort on Paradise Bay',
                        'address' => 'Cennet Koyu, Göltürkbükü',
                        'child_policy' => [
                            'max_children_per_room' => 2,
                            'child_age_limit' => 11,
                            'child_policies' => [
                                ['age_from' => 0, 'age_to' => 3, 'price' => 0],
                                ['age_from' => 4, 'age_to' => 11, 'price' => 200],
                            ],
                            'child_policy_description' => 'Children up to 3 years stay free. Extra bed charges apply for older children.'
                        ],
                        'refund_type' => 'refundable',
                        'cancellation_deadline' => 72,
                        'amenities' => ['Beach Access', 'Swimming Pool', 'Spa & Wellness', 'Restaurant', 'Bar'],
                        'rooms' => [
                            [
                                'name' => 'Sea View Room',
                                'type' => 'deluxe-room',
                                'capacity_adults' => 2,
                                'capacity_children' => 1,
                                'size' => 60,
                                'base_price' => 4500,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Balcony', 'Sea View'],
                                'board_types' => ['BB', 'HB']
                            ]
                        ]
                    ],
                    [
                        'name' => 'Voyage Bodrum',
                        'star_rating' => 5,
                        'type' => 'resort-hotel',
                        'description' => 'All-inclusive adults-only resort',
                        'address' => 'Torba Mahallesi, Bodrum',
                        'child_policy' => [
                            'max_children_per_room' => 0,
                            'child_age_limit' => 0,
                            'child_policies' => [],
                            'child_policy_description' => 'Adults only hotel (16+ years).'
                        ],
                        'refund_type' => 'both',
                        'cancellation_deadline' => 48,
                        'amenities' => ['Beach Access', 'Swimming Pool', 'Spa & Wellness', 'Restaurant', 'Bar', 'Fitness Center'],
                        'rooms' => [
                            [
                                'name' => 'Premium Room',
                                'type' => 'deluxe-room',
                                'capacity_adults' => 2,
                                'capacity_children' => 0,
                                'size' => 32,
                                'base_price' => 2200,
                                'amenities' => ['Air Conditioning', 'Mini Bar', 'Balcony'],
                                'board_types' => ['UAI']
                            ]
                        ]
                    ]
                ]
            ]
        ];
        
        foreach ($regionsData as $regionData) {
            // Create region
            $region = Region::firstOrCreate(
                ['slug' => Str::slug($regionData['name'])],
                [
                    'name' => $regionData['name'],
                    'type' => $regionData['type'],
                    'parent_id' => $turkey->id,
                    'is_active' => true
                ]
            );
            
            // Create hotels for this region
            foreach ($regionData['hotels'] as $hotelData) {
                $hotelType = HotelType::where('slug', $hotelData['type'])->first();
                
                $hotel = Hotel::create([
                    'name' => $hotelData['name'],
                    'slug' => Str::slug($hotelData['name']),
                    'region_id' => $region->id,
                    'hotel_type_id' => $hotelType->id,
                    'star_rating' => $hotelData['star_rating'],
                    'description' => $hotelData['description'],
                    'address' => $hotelData['address'],
                    'city' => $regionData['name'],
                    'country' => 'Turkey',
                    'is_active' => true,
                    'allow_refundable' => $hotelData['refund_type'] === 'refundable' || $hotelData['refund_type'] === 'both',
                    'allow_non_refundable' => $hotelData['refund_type'] === 'non_refundable' || $hotelData['refund_type'] === 'both',
                    'non_refundable_discount' => $hotelData['non_refundable_discount'] ?? 10,
                    'refund_policy' => json_encode([
                        'cancellation_deadline' => $hotelData['cancellation_deadline'] ?? 24,
                        'type' => $hotelData['refund_type']
                    ]),
                    'max_children_per_room' => $hotelData['child_policy']['max_children_per_room'],
                    'child_age_limit' => $hotelData['child_policy']['child_age_limit'],
                    'child_policies' => $hotelData['child_policy']['child_policies'],
                    'child_policy_description' => $hotelData['child_policy']['child_policy_description'],
                ]);
                
                // Attach amenities
                $amenityIds = HotelAmenity::whereIn('name', $hotelData['amenities'])->pluck('id');
                $hotel->amenities()->attach($amenityIds);
                
                // Create rooms
                foreach ($hotelData['rooms'] as $roomData) {
                    $roomType = RoomType::where('slug', $roomData['type'])->first();
                    
                    $room = Room::create([
                        'hotel_id' => $hotel->id,
                        'room_type_id' => $roomType->id,
                        'name' => $roomData['name'],
                        'slug' => $hotel->id . '-' . Str::slug($roomData['name']),
                        'capacity_adults' => $roomData['capacity_adults'],
                        'capacity_children' => $roomData['capacity_children'],
                        'size' => $roomData['size'],
                        'pricing_calculation_method' => 'per_room',
                        'is_active' => true,
                        'is_available' => true,
                    ]);
                    
                    // Attach room amenities
                    $roomAmenityIds = RoomAmenity::whereIn('name', $roomData['amenities'])->pluck('id');
                    $room->amenities()->attach($roomAmenityIds);
                    
                    // Create rate plans for each board type
                    foreach ($roomData['board_types'] as $boardTypeCode) {
                        $boardType = BoardType::where('code', $boardTypeCode)->first();
                        if ($boardType) {
                            RatePlan::create([
                                'hotel_id' => $hotel->id,
                                'room_id' => $room->id,
                                'board_type_id' => $boardType->id,
                                'is_per_person' => false,
                                'status' => true,
                            ]);
                        }
                    }
                }
            }
        }
        
        $this->command->info('Test data created successfully!');
    }
}