<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Hotel;

class ThemeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        // Öne çıkan bölgeleri getir
        $featuredRegions = Region::where('is_featured', true)
            ->where('is_active', true)
            ->withCount('hotels')
            ->take(6)
            ->get();
        
        // Öne çıkan otelleri getir
        $featuredHotels = Hotel::where('is_featured', true)
            ->where('is_active', true)
            ->with('region')
            ->take(8)
            ->get();
        
        // En popüler bölgeleri getir
        $popularRegions = Region::where('is_active', true)
            ->withCount('hotels')
            ->orderBy('hotels_count', 'desc')
            ->take(12)
            ->get();
            
        $viewData = [
            'featuredRegions' => $featuredRegions,
            'featuredHotels' => $featuredHotels,
            'popularRegions' => $popularRegions,
            'pageTitle' => 'Find Your Perfect Hotel',
            'pageDescription' => 'Search through thousands of hotels to find the perfect stay for your next trip.',
            'colorMode' => 'light'
        ];
            
        return view('theme.pages.home', $viewData);
    }
    
    /**
     * Show the about us page
     */
    public function about()
    {
        return view('theme.pages.static.about');
    }
    
    /**
     * Show the contact page
     */
    public function contact()
    {
        return view('theme.pages.static.contact');
    }
    
    /**
     * Show the terms page
     */
    public function terms()
    {
        return view('theme.pages.static.terms');
    }
    
    /**
     * Show the privacy policy page
     */
    public function privacy()
    {
        return view('theme.pages.static.privacy');
    }
    
    /**
     * Show the FAQ page
     */
    public function faq()
    {
        return view('theme.pages.static.faq');
    }
}