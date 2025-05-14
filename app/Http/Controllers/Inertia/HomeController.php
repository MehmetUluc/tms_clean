<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\Hotel;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        \Log::info('Inertia HomeController@index called');
        try {
            // Get featured regions
            $featuredRegions = Region::where('is_active', true)
                ->where('is_featured', true)
                ->withCount('hotels')
                ->orderBy('sort_order')
                ->limit(6)
                ->get();
                
            // Get popular hotels
            $popularHotels = Hotel::where('is_active', true)
                ->where('is_featured', true)
                ->with(['region'])
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();

            return Inertia::render('Home', [
                'featuredRegions' => $featuredRegions,
                'popularHotels' => $popularHotels,
                'metadata' => [
                    'title' => 'Find Your Perfect Stay',
                    'description' => 'Discover luxury accommodations for your next journey. Book hotels at the best prices with our easy-to-use platform.',
                    'keywords' => 'hotel booking, luxury hotels, vacation, travel, accommodation',
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in Inertia HomeController@index: ' . $e->getMessage());
            return Inertia::render('Error', [
                'status' => 500,
                'message' => 'An error occurred while loading the homepage'
            ]);
        }
    }
    
    /**
     * Display about page
     */
    public function about()
    {
        return Inertia::render('About', [
            'metadata' => [
                'title' => 'About Us',
                'description' => 'Learn about our company, mission, and commitment to providing exceptional hotel booking experiences.',
            ]
        ]);
    }
    
    /**
     * Display contact page
     */
    public function contact()
    {
        return Inertia::render('Contact', [
            'metadata' => [
                'title' => 'Contact Us',
                'description' => 'Get in touch with our customer support team. We\'re here to help with any questions or concerns.',
            ]
        ]);
    }
    
    /**
     * Display terms page
     */
    public function terms()
    {
        return Inertia::render('Terms', [
            'metadata' => [
                'title' => 'Terms of Service',
                'description' => 'Please read our terms of service carefully before using our platform.',
            ]
        ]);
    }
    
    /**
     * Display privacy policy page
     */
    public function privacy()
    {
        return Inertia::render('Privacy', [
            'metadata' => [
                'title' => 'Privacy Policy',
                'description' => 'Learn how we collect, use, and protect your personal information.',
            ]
        ]);
    }
    
    /**
     * Display FAQ page
     */
    public function faq()
    {
        return Inertia::render('FAQ', [
            'metadata' => [
                'title' => 'Frequently Asked Questions',
                'description' => 'Find answers to commonly asked questions about our services, booking process, and policies.',
            ]
        ]);
    }
}