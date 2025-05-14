<?php

namespace App\Plugins\Pricing\Repositories;

use App\Plugins\Pricing\Models\RatePlan;
use Illuminate\Support\Collection;

class RatePlanRepository
{
    /**
     * Find a rate plan by its ID
     *
     * @param int $id
     * @return RatePlan|null
     */
    public function find(int $id): ?RatePlan
    {
        return RatePlan::find($id);
    }

    /**
     * Get all rate plans
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return RatePlan::all();
    }

    /**
     * Get rate plans for a specific hotel
     *
     * @param int $hotelId
     * @return Collection
     */
    public function getByHotel(int $hotelId): Collection
    {
        return RatePlan::where('hotel_id', $hotelId)
                      ->with(['room', 'boardType'])
                      ->get();
    }

    /**
     * Get rate plans for a specific room
     *
     * @param int $roomId
     * @return Collection
     */
    public function getByRoom(int $roomId): Collection
    {
        return RatePlan::where('room_id', $roomId)
                      ->with(['boardType'])
                      ->get();
    }

    /**
     * Find a rate plan by hotel, room, and board type
     *
     * @param int $hotelId
     * @param int $roomId
     * @param int $boardTypeId
     * @return RatePlan|null
     */
    public function findByHotelRoomAndBoardType(int $hotelId, int $roomId, int $boardTypeId): ?RatePlan
    {
        return RatePlan::where([
            'hotel_id' => $hotelId,
            'room_id' => $roomId,
            'board_type_id' => $boardTypeId,
        ])->first();
    }

    /**
     * Create a new rate plan
     *
     * @param array $data
     * @return RatePlan
     */
    public function create(array $data): RatePlan
    {
        return RatePlan::create($data);
    }

    /**
     * Update a rate plan
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $ratePlan = $this->find($id);
        
        if (!$ratePlan) {
            return false;
        }
        
        return $ratePlan->update($data);
    }

    /**
     * Delete a rate plan
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $ratePlan = $this->find($id);
        
        if (!$ratePlan) {
            return false;
        }
        
        return $ratePlan->delete();
    }

    /**
     * Create or update a rate plan
     *
     * @param int $hotelId
     * @param int $roomId
     * @param int $boardTypeId
     * @param bool $isPerPerson
     * @return RatePlan
     */
    public function createOrUpdate(int $hotelId, int $roomId, int $boardTypeId, bool $isPerPerson): RatePlan
    {
        $ratePlan = $this->findByHotelRoomAndBoardType($hotelId, $roomId, $boardTypeId);
        
        if ($ratePlan) {
            $ratePlan->is_per_person = $isPerPerson;
            $ratePlan->save();
            return $ratePlan;
        }
        
        return $this->create([
            'hotel_id' => $hotelId,
            'room_id' => $roomId,
            'board_type_id' => $boardTypeId,
            'is_per_person' => $isPerPerson,
            'status' => true,
        ]);
    }
}