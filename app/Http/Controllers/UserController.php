<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get recent reservations
        $recentReservations = $user->reservations()
            ->with('room.hotel')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        return view('theme.pages.profile.index', compact('user', 'recentReservations'));
    }
    
    /**
     * Display the user's reservations.
     */
    public function reservations()
    {
        $user = Auth::user();
        
        // Get all user reservations with pagination
        $reservations = $user->reservations()
            ->with('room.hotel')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('theme.pages.profile.reservations', compact('reservations'));
    }
    
    /**
     * Display a specific reservation.
     */
    public function showReservation(Reservation $reservation)
    {
        // Check if the reservation belongs to the user
        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Load reservation with related data
        $reservation->load('room.hotel', 'room.roomType', 'guests');
        
        return view('theme.pages.profile.reservation-detail', compact('reservation'));
    }
    
    /**
     * Display the user's account settings.
     */
    public function settings()
    {
        return view('theme.pages.profile.settings', [
            'user' => Auth::user()
        ]);
    }
    
    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        // Update the user
        $user->update($validated);
        
        return redirect()->route('profile.settings')
            ->with('status', 'profile-updated');
    }
    
    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Update the password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return redirect()->route('profile.settings')
            ->with('status', 'password-updated');
    }
}