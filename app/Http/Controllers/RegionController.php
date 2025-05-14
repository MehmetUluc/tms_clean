<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of regions
     */
    public function index()
    {
        // Get top-level regions
        $topRegions = Region::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('hotels')
            ->orderBy('name')
            ->get();
            
        // Get popular regions based on hotel count
        $popularRegions = Region::where('is_active', true)
            ->withCount('hotels')
            ->orderBy('hotels_count', 'desc')
            ->limit(12)
            ->get();
            
        return view('theme.pages.regions.index', compact('topRegions', 'popularRegions'));
    }
    
    /**
     * Display a specific region
     */
    public function show(Region $region)
    {
        // Load relationships
        $region->load('children', 'parent');
        
        // Get hotels in this region
        $hotels = $region->hotels()
            ->where('is_active', true)
            ->with('region', 'hotelType')
            ->paginate(12);
            
        // Get child regions if any
        $childRegions = $region->children()
            ->where('is_active', true)
            ->withCount('hotels')
            ->get();
            
        // Get related regions (siblings if it has a parent, otherwise top popular regions)
        if ($region->parent_id) {
            $relatedRegions = Region::where('parent_id', $region->parent_id)
                ->where('id', '!=', $region->id)
                ->where('is_active', true)
                ->withCount('hotels')
                ->limit(6)
                ->get();
        } else {
            $relatedRegions = Region::where('is_active', true)
                ->whereNull('parent_id')
                ->where('id', '!=', $region->id)
                ->withCount('hotels')
                ->limit(6)
                ->get();
        }
        
        return view('theme.pages.regions.show', compact(
            'region', 
            'hotels', 
            'childRegions', 
            'relatedRegions'
        ));
    }
}