<?php

namespace App\Http\Controllers\B2C;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\Hotel;

class HomeController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        \Log::info('HomeController@index called');
        try {
            return view('b2c.pages.home', [
                'pageTitle' => 'Find Your Perfect Stay',
                'pageDescription' => 'Discover luxury accommodations for your next journey. Book hotels at the best prices with our easy-to-use platform.',
                'pageKeywords' => 'hotel booking, luxury hotels, vacation, travel, accommodation',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in HomeController@index: ' . $e->getMessage());
            return 'Error: ' . $e->getMessage() . '<br>File: ' . $e->getFile() . '<br>Line: ' . $e->getLine();
        }
    }
    
    /**
     * Display about page
     */
    public function about()
    {
        return view('b2c.pages.static.about', [
            'pageTitle' => 'About Us',
            'pageDescription' => 'Learn about our company, mission, and commitment to providing exceptional hotel booking experiences.',
        ]);
    }
    
    /**
     * Display contact page
     */
    public function contact()
    {
        return view('b2c.pages.static.contact', [
            'pageTitle' => 'Contact Us',
            'pageDescription' => 'Get in touch with our customer support team. We\'re here to help with any questions or concerns.',
        ]);
    }
    
    /**
     * Display terms page
     */
    public function terms()
    {
        return view('b2c.pages.static.terms', [
            'pageTitle' => 'Terms of Service',
            'pageDescription' => 'Please read our terms of service carefully before using our platform.',
        ]);
    }
    
    /**
     * Display privacy policy page
     */
    public function privacy()
    {
        return view('b2c.pages.static.privacy', [
            'pageTitle' => 'Privacy Policy',
            'pageDescription' => 'Learn how we collect, use, and protect your personal information.',
        ]);
    }
    
    /**
     * Display FAQ page
     */
    public function faq()
    {
        return view('b2c.pages.static.faq', [
            'pageTitle' => 'Frequently Asked Questions',
            'pageDescription' => 'Find answers to commonly asked questions about our services, booking process, and policies.',
        ]);
    }
}