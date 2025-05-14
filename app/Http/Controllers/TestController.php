<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plugins\Accommodation\Models\Hotel;

class TestController extends Controller
{
    /**
     * Show hotel data
     */
    public function index()
    {
        $hotels = Hotel::all();
        
        return response()->json([
            'hotels' => $hotels,
            'count' => $hotels->count()
        ]);
    }
}
