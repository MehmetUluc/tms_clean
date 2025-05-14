<?php

namespace App\Http\Controllers;

use App\Plugins\Accommodation\Models\Hotel;
use App\Models\Region;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of hotels with filtering
     */
    public function index(Request $request)
    {
        // Initialize query
        $query = Hotel::where('is_active', true)
            ->with('region', 'amenities');
            
        // Filter by region if provided
        if ($request->has('region')) {
            $query->where('region_id', $request->region);
        }
        
        // Filter by hotel type if provided
        if ($request->has('type')) {
            $query->where('hotel_type_id', $request->type);
        }
        
        // Filter by star rating if provided
        if ($request->has('stars')) {
            $query->where('stars', $request->stars);
        }
        
        // Sort the results
        $sortBy = $request->sort ?? 'popularity';
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('min_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('min_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popularity':
            default:

                break;
        }
        
        // Paginate the results
        $hotels = $query->paginate(12)->withQueryString();
        
        // Get hotel types and regions for filters
        $hotelTypes = \App\Models\HotelType::all();
        $regions = Region::where('is_active', true)->get();
        
        // Get search parameters for the view
        $searchParams = [
            'region' => $request->region,
            'check_in' => $request->check_in ?? now()->addDay()->format('Y-m-d'),
            'check_out' => $request->check_out ?? now()->addDays(3)->format('Y-m-d'),
            'adults' => $request->adults ?? 2,
            'children' => $request->children ?? 0,
            'children_ages' => $request->children_ages ?? '',
            'rooms' => $request->rooms ?? 1,
        ];
        
        return view('theme.pages.hotels.index', compact(
            'hotels', 
            'hotelTypes', 
            'regions', 
            'searchParams'
        ));
    }
    
    /**
     * Display a specific hotel
     */
    public function show(Hotel $hotel)
    {
        // Load relationships
        $hotel->load('region', 'hotelType', 'amenities', 'rooms.roomType', 'gallery');
        
        // Get similar hotels in the same region
        $similarHotels = Hotel::where('region_id', $hotel->region_id)
            ->where('id', '!=', $hotel->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();
            
        return view('theme.pages.hotels.show', compact('hotel', 'similarHotels'));
    }
    
    /**
     * Show rooms for a specific hotel
     */
    public function rooms(Request $request, Hotel $hotel)
    {
        // Load relationships
        $hotel->load('rooms.roomType', 'rooms.amenities', 'rooms.boardTypes');
        
        // Prepare search parameters
        $searchParams = [
            'check_in' => $request->check_in ?? now()->addDay()->format('Y-m-d'),
            'check_out' => $request->check_out ?? now()->addDays(3)->format('Y-m-d'),
            'adults' => $request->adults ?? 2,
            'children' => $request->children ?? 0,
            'children_ages' => $request->children_ages ?? '',
            'rooms' => $request->rooms ?? 1,
        ];
        
        return view('theme.pages.hotels.rooms', compact('hotel', 'searchParams'));
    }
}