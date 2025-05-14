<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\Hotel;
use Inertia\Inertia;

class DestinationController extends Controller
{
    /**
     * Display a listing of destinations (regions)
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
            
        return Inertia::render('Destinations', [
            'regions' => $regions,
            'featuredRegions' => $featuredRegions,
            'metadata' => [
                'title' => 'Destinations',
                'description' => 'Explore our featured destinations and find the perfect location for your next trip',
            ],
        ]);
    }
    
    /**
     * Display the specified destination (region)
     */
    public function show(Region $destination)
    {
        // Check if region is active
        if (!$destination->is_active) {
            abort(404);
        }
        
        $destination->load('hotels');
        
        // Get active hotels for this region
        $hotels = Hotel::where('region_id', $destination->id)
            ->where('is_active', true)
            ->with(['region', 'type', 'tags'])
            ->paginate(12);
            
        // Get child regions if any
        $childRegions = Region::where('parent_id', $destination->id)
            ->where('is_active', true)
            ->withCount('hotels')
            ->get();
            
        return Inertia::render('DestinationDetail', [
            'destination' => $destination,
            'hotels' => $hotels,
            'childRegions' => $childRegions,
            'metadata' => [
                'title' => $destination->name,
                'description' => $destination->description ?? 'Explore hotels in ' . $destination->name,
            ],
        ]);
    }
}