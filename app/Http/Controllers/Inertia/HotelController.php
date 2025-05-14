<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\HotelType;
use App\Plugins\Amenities\Models\HotelAmenity;
use Inertia\Inertia;

class HotelController extends Controller
{
    /**
     * Display a listing of hotels
     */
    public function index(Request $request)
    {
        // Filter parameters
        $regionId = $request->input('region');
        $typeId = $request->input('type');
        $stars = $request->input('stars');
        $amenities = $request->input('amenities');
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $checkInDate = $request->input('check_in');
        $checkOutDate = $request->input('check_out');
        $adults = $request->input('adults', 2);
        $children = $request->input('children', 0);
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'recommended');
        
        // Base query
        $query = Hotel::where('is_active', true);
        
        // Apply region filter
        if ($regionId) {
            $query->where('region_id', $regionId);
        }
        
        // Apply type filter
        if ($typeId) {
            $query->where('type_id', $typeId);
        }
        
        // Apply stars filter
        if ($stars) {
            if (is_array($stars)) {
                $query->whereIn('star_rating', $stars);
            } else {
                $query->where('star_rating', $stars);
            }
        }
        
        // Apply amenities filter
        if ($amenities) {
            $amenitiesArray = is_array($amenities) ? $amenities : [$amenities];
            foreach ($amenitiesArray as $amenity) {
                $query->whereRaw("JSON_CONTAINS(amenities, '\"$amenity\"')");
            }
        }
        
        // Apply price range filter (commented out since columns don't exist)
        // Prices will be handled in frontend for now
        /*
        if ($priceMin) {
            $query->where('min_price', '>=', $priceMin);
        }
        
        if ($priceMax) {
            $query->where('max_price', '<=', $priceMax);
        }
        */
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('sort_order', 'asc'); // replacing min_price with sort_order
                break;
            case 'price_high':
                $query->orderBy('sort_order', 'desc'); // replacing min_price with sort_order
                break;
            case 'rating':
                $query->orderBy('star_rating', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'recommended':
            default:
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('star_rating', 'desc')
                      ->orderBy('sort_order', 'asc'); // replacing min_price with sort_order
                break;
        }
        
        // Execute the query with eager loading
        $hotels = $query->with([
            'region', 
            'type', 
            'tags', 
            'amenities'
        ])->paginate(12);
        
        // Get filter data for the sidebar
        $regions = Region::where('is_active', true)->orderBy('name')->get();
        $hotelTypes = HotelType::orderBy('name')->get();
        $allAmenities = HotelAmenity::orderBy('name')->get();
        
        // Price range for the filter (hardcoded for now since columns don't exist)
        $priceRange = (object)[
            'min' => 50,
            'max' => 1000
        ];
            
        // Search parameters (for maintaining search form data)
        $searchParams = [
            'region' => $regionId,
            'check_in' => $checkInDate ?? now()->addDay()->format('Y-m-d'),
            'check_out' => $checkOutDate ?? now()->addDays(3)->format('Y-m-d'),
            'adults' => $adults,
            'children' => $children,
            'search' => $search,
            'sort_by' => $sortBy,
        ];
        
        return Inertia::render('Hotels', [
            'hotels' => $hotels,
            'regions' => $regions,
            'hotelTypes' => $hotelTypes,
            'amenities' => $allAmenities,
            'priceRange' => $priceRange,
            'searchParams' => $searchParams,
            'metadata' => [
                'title' => 'Hotels',
                'description' => 'Find and book hotels at the best prices for your next trip',
            ],
        ]);
    }
    
    /**
     * Display the specified hotel
     */
    public function show($id)
    {
        try {
            // Try to find the hotel by ID
            $hotel = Hotel::findOrFail($id);
            
            // Check if hotel is active
            if (!$hotel->is_active) {
                // Show demo data instead (for demonstration purposes)
                return $this->showDemoHotel($id);
            }
            
            // Load relationships
            $hotel->load([
                'region', 
                'type', 
                'tags', 
                'amenities', 
                'rooms.roomType', 
                'rooms.boardTypes', 
                'rooms.amenities'
            ]);
            
            // Get similar hotels
            $similarHotels = Hotel::where('id', '!=', $hotel->id)
                ->where('is_active', true)
                ->where(function($query) use ($hotel) {
                    $query->where('region_id', $hotel->region_id)
                          ->orWhere('type_id', $hotel->type_id);
                })
                ->limit(4)
                ->with(['region'])
                ->get();
                
            return Inertia::render('HotelDetail', [
                'hotel' => $hotel,
                'similarHotels' => $similarHotels,
                'metadata' => [
                    'title' => $hotel->name,
                    'description' => $hotel->short_description ?? 'Book your stay at ' . $hotel->name,
                ],
            ]);
        } catch (\Exception $e) {
            // If hotel not found, show demo data
            return $this->showDemoHotel($id);
        }
    }
    
    /**
     * Show a demo hotel for demonstration
     */
    protected function showDemoHotel($id)
    {
        // Create a demo hotel object with the requested ID
        $hotel = [
            'id' => $id,
            'name' => 'Grand Resort & Spa',
            'location' => 'Antalya, Turkey',
            'address' => 'Lara Beach Road, 07230 Antalya, Turkey',
            'description' => 'Luxurious beachfront resort with spectacular views of the Mediterranean Sea, featuring elegant rooms and world-class dining.',
            'descriptionLong' => 'Located directly on Lara Beach with stunning views of the Mediterranean Sea, Grand Resort & Spa offers the perfect blend of luxury, comfort, and Turkish hospitality. Our spacious rooms and suites are elegantly designed with modern amenities and private balconies overlooking the sea or our lush gardens. Indulge in our restaurants offering international and local cuisines, relax by our swimming pools, or rejuvenate at our spa.',
            'rating' => 4.8,
            'reviewCount' => 642,
            'price' => 189,
            'oldPrice' => 229,
            'images' => [
                '/images/placeholder.jpg',
                '/images/placeholder.jpg',
                '/images/placeholder.jpg',
                '/images/placeholder.jpg',
                '/images/placeholder.jpg',
            ],
            'amenitiesByCategory' => [
                'General' => ['Free WiFi', '24-hour front desk', 'Airport shuttle', 'Room service'],
                'Wellness' => ['Spa', 'Fitness center', 'Sauna', 'Massage'],
                'Food & Drink' => ['Restaurant', 'Bar/Lounge', 'Breakfast available'],
                'Activities' => ['Swimming pool', 'Private beach', 'Tennis court'],
            ],
            'roomTypes' => [
                [
                    'name' => 'Deluxe Sea View Room',
                    'description' => 'Spacious room with private balcony overlooking the Mediterranean Sea.',
                    'price' => 189,
                    'occupancy' => 'Sleeps 2',
                    'image' => '/images/placeholder.jpg',
                    'features' => ['Sea View', 'Balcony', 'Air conditioning', 'Free WiFi'],
                    'freeCancellation' => true
                ],
                [
                    'name' => 'Superior Garden View Room',
                    'description' => 'Elegant room with views of the resort\'s lush gardens.',
                    'price' => 159,
                    'occupancy' => 'Sleeps 2',
                    'image' => '/images/placeholder.jpg',
                    'features' => ['Garden View', 'Sitting Area', 'Air conditioning', 'Free WiFi'],
                    'freeCancellation' => true
                ],
            ],
            'reviews' => [
                [
                    'author' => 'John D.',
                    'date' => 'October 2024',
                    'rating' => 5.0,
                    'comment' => 'Absolutely fantastic resort! The staff were incredibly attentive and the rooms were spacious and clean.'
                ],
                [
                    'author' => 'Maria S.',
                    'date' => 'September 2024',
                    'rating' => 4.5,
                    'comment' => 'We had a wonderful stay at Grand Resort. The spa treatments were amazing.'
                ],
            ],
            'pointsOfInterest' => [
                ['name' => 'Antalya International Airport', 'distance' => '15 km / 20 min drive'],
                ['name' => 'Antalya Old Town', 'distance' => '20 km / 30 min drive'],
                ['name' => 'Lara Beach', 'distance' => 'Beachfront'],
            ]
        ];
        
        // Dummy similar hotels
        $similarHotels = [
            [
                'id' => $id + 1,
                'name' => 'Azure Bay Hotel',
                'location' => 'Bodrum, Turkey',
                'image' => '/images/placeholder.jpg',
                'rating' => 4.6,
                'price' => 159,
            ],
            [
                'id' => $id + 2,
                'name' => 'Mountain View Lodge',
                'location' => 'Cappadocia, Turkey',
                'image' => '/images/placeholder.jpg',
                'rating' => 4.4,
                'price' => 129,
            ],
        ];
        
        return Inertia::render('HotelDetail', [
            'hotel' => $hotel,
            'similarHotels' => $similarHotels,
            'metadata' => [
                'title' => $hotel['name'],
                'description' => 'Book your stay at ' . $hotel['name'],
            ],
        ]);
    }
    
    /**
     * Display rooms for the specified hotel
     */
    public function rooms(Request $request, Hotel $hotel)
    {
        // Check if hotel is active
        if (!$hotel->is_active) {
            abort(404);
        }
        
        // Load relationships
        $hotel->load([
            'rooms.roomType', 
            'rooms.boardTypes', 
            'rooms.amenities'
        ]);
        
        // Search parameters
        $searchParams = [
            'check_in' => $request->input('check_in', now()->addDay()->format('Y-m-d')),
            'check_out' => $request->input('check_out', now()->addDays(3)->format('Y-m-d')),
            'adults' => $request->input('adults', 2),
            'children' => $request->input('children', 0),
        ];
        
        return Inertia::render('HotelRooms', [
            'hotel' => $hotel,
            'searchParams' => $searchParams,
            'metadata' => [
                'title' => $hotel->name . ' - Rooms',
                'description' => 'Browse available rooms and rates at ' . $hotel->name,
            ],
        ]);
    }
}