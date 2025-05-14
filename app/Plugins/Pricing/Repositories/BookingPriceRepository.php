<?php

namespace App\Plugins\Pricing\Repositories;

use App\Plugins\Pricing\Models\BookingPrice;
use Illuminate\Support\Collection;

class BookingPriceRepository
{
    /**
     * Find a booking price by its ID
     *
     * @param int $id
     * @return BookingPrice|null
     */
    public function find(int $id): ?BookingPrice
    {
        return BookingPrice::find($id);
    }

    /**
     * Get all booking prices for a reservation
     *
     * @param int $reservationId
     * @return Collection
     */
    public function getByReservation(int $reservationId): Collection
    {
        return BookingPrice::where('reservation_id', $reservationId)->get();
    }

    /**
     * Create a new booking price
     *
     * @param array $data
     * @return BookingPrice
     */
    public function create(array $data): BookingPrice
    {
        return BookingPrice::create($data);
    }

    /**
     * Update a booking price
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $bookingPrice = $this->find($id);
        
        if (!$bookingPrice) {
            return false;
        }
        
        return $bookingPrice->update($data);
    }

    /**
     * Delete a booking price
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $bookingPrice = $this->find($id);
        
        if (!$bookingPrice) {
            return false;
        }
        
        return $bookingPrice->delete();
    }

    /**
     * Delete all booking prices for a reservation
     *
     * @param int $reservationId
     * @return bool
     */
    public function deleteByReservation(int $reservationId): bool
    {
        return BookingPrice::where('reservation_id', $reservationId)->delete();
    }

    /**
     * Create multiple booking prices at once
     *
     * @param array $data
     * @return Collection
     */
    public function createMany(array $data): Collection
    {
        $bookingPrices = collect();
        
        foreach ($data as $item) {
            $bookingPrices->push($this->create($item));
        }
        
        return $bookingPrices;
    }

    /**
     * Calculate total price for a reservation
     *
     * @param int $reservationId
     * @return float
     */
    public function calculateTotalPrice(int $reservationId): float
    {
        return BookingPrice::where('reservation_id', $reservationId)
                         ->sum('price');
    }
}