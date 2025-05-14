<?php

namespace App\Http\Controllers\B2C;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\HotelType;
use App\Plugins\Amenities\Models\HotelAmenity;

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
                $query->whereIn('stars', $stars);
            } else {
                $query->where('stars', $stars);
            }
        }
        
        // Apply amenities filter
        if ($amenities) {
            $amenitiesArray = is_array($amenities) ? $amenities : [$amenities];
            foreach ($amenitiesArray as $amenity) {
                $query->whereRaw("JSON_CONTAINS(amenities, '\"$amenity\"')");
            }
        }
        
        // Apply price range filter
        if ($priceMin) {
            $query->where('min_price', '>=', $priceMin);
        }
        
        if ($priceMax) {
            $query->where('max_price', '<=', $priceMax);
        }
        
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
                $query->orderBy('min_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('min_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('stars', 'desc');
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
                      ->orderBy('stars', 'desc')
                      ->orderBy('min_price', 'asc');
                break;
        }
        
        // Execute the query with eager loading
        $hotels = $query->with([
            'region', 
            'type', 
            'tags', 
            'amenities'
        ])->paginate(12)->withQueryString();
        
        // Get filter data for the sidebar
        $regions = Region::where('is_active', true)->orderBy('name')->get();
        $hotelTypes = HotelType::orderBy('name')->get();
        $allAmenities = HotelAmenity::orderBy('name')->get();
        
        // Price range for the filter
        $priceRange = Hotel::where('is_active', true)
            ->selectRaw('MIN(min_price) as min, MAX(max_price) as max')
            ->first();
            
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
        
        return view('b2c.pages.hotels.index', [
            'hotels' => $hotels,
            'regions' => $regions,
            'hotelTypes' => $hotelTypes,
            'amenities' => $allAmenities,
            'priceRange' => $priceRange,
            'searchParams' => $searchParams,
            'pageTitle' => 'Hotels',
            'pageDescription' => 'Find and book hotels at the best prices for your next trip',
        ]);
    }
    
    /**
     * Display the specified hotel
     */
    public function show(Hotel $hotel)
    {
        // Check if hotel is active
        if (!$hotel->is_active) {
            abort(404);
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
            ->get();
            
        return view('b2c.pages.hotels.show', [
            'hotel' => $hotel,
            'similarHotels' => $similarHotels,
            'pageTitle' => $hotel->name,
            'pageDescription' => $hotel->short_description ?? 'Book your stay at ' . $hotel->name,
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
        
        return view('b2c.pages.hotels.rooms', [
            'hotel' => $hotel,
            'searchParams' => $searchParams,
            'pageTitle' => $hotel->name . ' - Rooms',
            'pageDescription' => 'Browse available rooms and rates at ' . $hotel->name,
        ]);
    }
}