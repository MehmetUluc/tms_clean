<?php

namespace App\Plugins\Pricing\Services;

use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Pricing\Models\DailyRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Check room availability for a date range
     *
     * @param int $roomId
     * @param string $checkIn
     * @param string $checkOut
     * @return array
     */
    public function checkAvailability(int $roomId, string $checkIn, string $checkOut): array
    {
        $room = Room::find($roomId);
        if (!$room) {
            return [
                'is_available' => false,
                'available_rooms' => 0,
                'message' => 'Room not found',
                'reason' => 'not_found',
            ];
        }
        
        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        
        // Get daily rates for the period
        $dailyRates = DailyRate::whereIn('rate_plan_id', function($query) use ($roomId) {
                $query->select('id')
                    ->from('rate_plans')
                    ->where('room_id', $roomId)
                    ->where('status', true);
            })
            ->whereBetween('date', [$checkInDate, $checkOutDate->subDay()])
            ->get();
        
        // Check if any day is closed
        foreach ($dailyRates as $dailyRate) {
            if ($dailyRate->is_closed) {
                return [
                    'is_available' => false,
                    'available_rooms' => 0,
                    'message' => 'Room not available on ' . $dailyRate->date->format('d M Y'),
                    'reason' => 'closed',
                ];
            }
        }
        
        // Check existing reservations
        $minAvailable = $this->getMinimumAvailableRooms($roomId, $checkInDate, $checkOutDate);
        
        return [
            'is_available' => $minAvailable > 0,
            'available_rooms' => $minAvailable,
            'message' => $minAvailable > 0 ? 'Available' : 'No rooms available',
            'reason' => $minAvailable > 0 ? null : 'no_inventory',
        ];
    }
    
    /**
     * Get minimum available rooms for a date range
     *
     * @param int $roomId
     * @param Carbon $checkIn
     * @param Carbon $checkOut
     * @return int
     */
    public function getMinimumAvailableRooms(int $roomId, Carbon $checkIn, Carbon $checkOut): int
    {
        $room = Room::find($roomId);
        if (!$room) {
            return 0;
        }
        
        // Default room count if not specified
        $totalRooms = $room->room_count ?? 10;
        $minAvailable = $totalRooms;
        
        // Check each day in the range
        $currentDate = $checkIn->copy();
        while ($currentDate < $checkOut) {
            $dayAvailable = $this->getAvailableRoomsForDate($roomId, $currentDate, $totalRooms);
            $minAvailable = min($minAvailable, $dayAvailable);
            
            if ($minAvailable <= 0) {
                break;
            }
            
            $currentDate->addDay();
        }
        
        return max(0, $minAvailable);
    }
    
    /**
     * Get available rooms for a specific date
     *
     * @param int $roomId
     * @param Carbon $date
     * @param int $totalRooms
     * @return int
     */
    protected function getAvailableRoomsForDate(int $roomId, Carbon $date, int $totalRooms): int
    {
        // Count confirmed reservations for this date
        $bookedRooms = Reservation::where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where('check_in_date', '<=', $date)
            ->where('check_out_date', '>', $date)
            ->count();
        
        // Get inventory override from daily rates if exists
        $dailyRate = DailyRate::whereIn('rate_plan_id', function($query) use ($roomId) {
                $query->select('id')
                    ->from('rate_plans')
                    ->where('room_id', $roomId)
                    ->where('status', true);
            })
            ->where('date', $date->format('Y-m-d'))
            ->first();
        
        if ($dailyRate && $dailyRate->inventory !== null) {
            // Use inventory from daily rate as available rooms
            return max(0, $dailyRate->inventory - $bookedRooms);
        }
        
        // Otherwise use total rooms minus booked
        return max(0, $totalRooms - $bookedRooms);
    }
    
    /**
     * Reserve rooms (decrease availability)
     *
     * @param int $roomId
     * @param string $checkIn
     * @param string $checkOut
     * @param int $quantity
     * @return bool
     */
    public function reserveRooms(int $roomId, string $checkIn, string $checkOut, int $quantity = 1): bool
    {
        // Check availability first
        $availability = $this->checkAvailability($roomId, $checkIn, $checkOut);
        
        if (!$availability['is_available'] || $availability['available_rooms'] < $quantity) {
            return false;
        }
        
        // In a real system, we might update inventory tables here
        // For now, the reservation itself acts as the inventory decrement
        
        return true;
    }
    
    /**
     * Release rooms (increase availability)
     *
     * @param int $reservationId
     * @return bool
     */
    public function releaseRooms(int $reservationId): bool
    {
        // In a real system, we might update inventory tables here
        // For now, cancelling the reservation releases the inventory
        
        return true;
    }
    
    /**
     * Get availability calendar for a room
     *
     * @param int $roomId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getAvailabilityCalendar(int $roomId, string $startDate, string $endDate): array
    {
        $room = Room::find($roomId);
        if (!$room) {
            return [];
        }
        
        $calendar = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $totalRooms = $room->room_count ?? 10;
        
        $currentDate = $start->copy();
        while ($currentDate <= $end) {
            $available = $this->getAvailableRoomsForDate($roomId, $currentDate, $totalRooms);
            
            $calendar[$currentDate->format('Y-m-d')] = [
                'date' => $currentDate->format('Y-m-d'),
                'available' => $available,
                'total' => $totalRooms,
                'booked' => $totalRooms - $available,
                'is_available' => $available > 0,
            ];
            
            $currentDate->addDay();
        }
        
        return $calendar;
    }
    
    /**
     * Bulk check availability for multiple rooms
     *
     * @param array $roomIds
     * @param string $checkIn
     * @param string $checkOut
     * @return array
     */
    public function bulkCheckAvailability(array $roomIds, string $checkIn, string $checkOut): array
    {
        $results = [];
        
        foreach ($roomIds as $roomId) {
            $results[$roomId] = $this->checkAvailability($roomId, $checkIn, $checkOut);
        }
        
        return $results;
    }
    
    /**
     * Check if a room is set as Sale on Request (SOR) for the given dates
     *
     * @param int $roomId
     * @param Carbon $checkIn
     * @param Carbon $checkOut
     * @return bool
     */
    public function isSaleOnRequest(int $roomId, Carbon $checkIn, Carbon $checkOut): bool
    {
        // Check if any daily rate in the period has SOR flag
        $hasSOR = DailyRate::whereIn('rate_plan_id', function($query) use ($roomId) {
                $query->select('id')
                    ->from('rate_plans')
                    ->where('room_id', $roomId)
                    ->where('status', true);
            })
            ->whereBetween('date', [$checkIn, $checkOut->subDay()])
            ->where('is_sor', true) // Assuming we have is_sor column
            ->exists();
            
        return $hasSOR;
    }
    
    /**
     * Set stop sale for a room on specific dates
     *
     * @param int $roomId
     * @param array $dates
     * @return bool
     */
    public function setStopSale(int $roomId, array $dates): bool
    {
        try {
            foreach ($dates as $date) {
                DailyRate::whereIn('rate_plan_id', function($query) use ($roomId) {
                        $query->select('id')
                            ->from('rate_plans')
                            ->where('room_id', $roomId);
                    })
                    ->where('date', Carbon::parse($date)->format('Y-m-d'))
                    ->update(['is_closed' => true]);
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error setting stop sale: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove stop sale for a room on specific dates
     *
     * @param int $roomId
     * @param array $dates
     * @return bool
     */
    public function removeStopSale(int $roomId, array $dates): bool
    {
        try {
            foreach ($dates as $date) {
                DailyRate::whereIn('rate_plan_id', function($query) use ($roomId) {
                        $query->select('id')
                            ->from('rate_plans')
                            ->where('room_id', $roomId);
                    })
                    ->where('date', Carbon::parse($date)->format('Y-m-d'))
                    ->update(['is_closed' => false]);
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error removing stop sale: ' . $e->getMessage());
            return false;
        }
    }
}