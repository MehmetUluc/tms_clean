<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Show search results
     */
    public function search(Request $request)
    {
        // Redirect to hotels index with search parameters
        return redirect()->route('hotels.index', $request->all());
    }
    
    /**
     * Show hotel booking page
     */
    public function hotel(Request $request, Hotel $hotel)
    {
        // Load hotel with rooms and related data
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
        
        return view('theme.pages.booking.hotel', compact('hotel', 'searchParams'));
    }
    
    /**
     * Show room booking page
     */
    public function room(Request $request, Room $room)
    {
        // Load room with related data
        $room->load('hotel', 'roomType', 'amenities', 'boardTypes');
        
        // Prepare search parameters
        $searchParams = [
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'adults' => $request->adults,
            'children' => $request->children,
            'children_ages' => $request->children_ages,
            'rooms' => $request->rooms,
            'board_type' => $request->board_type,
        ];
        
        return view('theme.pages.booking.room', compact('room', 'searchParams'));
    }
    
    /**
     * Show guest information form
     */
    public function guestInfo(Request $request)
    {
        // Check if we have all required parameters
        if (!$request->has(['room_id', 'check_in', 'check_out', 'adults', 'children', 'board_type'])) {
            return redirect()->route('home')->with('error', 'Missing required booking parameters');
        }
        
        // Load room data
        $room = Room::with('hotel', 'roomType', 'boardTypes')->findOrFail($request->room_id);
        
        // Booking parameters
        $bookingParams = $request->all();
        
        return view('theme.pages.booking.guest-info', compact('room', 'bookingParams'));
    }
    
    /**
     * Show payment form
     */
    public function payment(Request $request)
    {
        // Process guest information and prepare for payment
        // Note: In a real application, you would validate the guest information here
        
        // Load room data
        $room = Room::with('hotel', 'roomType', 'boardTypes')->findOrFail($request->room_id);
        
        // Booking parameters and guest information
        $bookingParams = $request->all();
        
        return view('theme.pages.booking.payment', compact('room', 'bookingParams'));
    }
    
    /**
     * Show booking confirmation
     */
    public function confirmation(Reservation $reservation)
    {
        // Check if the current user owns the reservation
        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Load reservation with related data
        $reservation->load('room.hotel', 'room.roomType', 'guests');
        
        return view('theme.pages.booking.confirmation', compact('reservation'));
    }
}