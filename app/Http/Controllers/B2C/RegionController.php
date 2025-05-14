<?php

namespace App\Http\Controllers\B2C;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\Hotel;

class RegionController extends Controller
{
    /**
     * Display a listing of regions
     */
    public function index()
    {
        $regions = Region::where('is_active', true)
            ->withCount('hotels')
            ->orderBy('name')
            ->get();
            
        $featuredRegions = Region::where('is_active', true)
            ->where('is_featured', true)
            ->withCount('hotels')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
            
        return view('b2c.pages.regions.index', [
            'regions' => $regions,
            'featuredRegions' => $featuredRegions,
            'pageTitle' => 'Destinations',
            'pageDescription' => 'Explore our featured destinations and find the perfect location for your next trip',
        ]);
    }
    
    /**
     * Display the specified region
     */
    public function show(Region $region)
    {
        // Check if region is active
        if (!$region->is_active) {
            abort(404);
        }
        
        $region->load('hotels');
        
        // Get active hotels for this region
        $hotels = Hotel::where('region_id', $region->id)
            ->where('is_active', true)
            ->with(['region', 'type', 'tags'])
            ->paginate(12);
            
        // Get child regions if any
        $childRegions = Region::where('parent_id', $region->id)
            ->where('is_active', true)
            ->withCount('hotels')
            ->get();
            
        return view('b2c.pages.regions.show', [
            'region' => $region,
            'hotels' => $hotels,
            'childRegions' => $childRegions,
            'pageTitle' => $region->name,
            'pageDescription' => $region->description ?? 'Explore hotels in ' . $region->name,
        ]);
    }
}