<?php

namespace App\Plugins\Pricing\Repositories;

use App\Plugins\Pricing\Models\RateException;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RateExceptionRepository
{
    /**
     * Find a rate exception by its ID
     *
     * @param int $id
     * @return RateException|null
     */
    public function find(int $id): ?RateException
    {
        return RateException::find($id);
    }

    /**
     * Get all exceptions for a rate period
     *
     * @param int $ratePeriodId
     * @return Collection
     */
    public function getByRatePeriod(int $ratePeriodId): Collection
    {
        return RateException::where('rate_period_id', $ratePeriodId)->get();
    }

    /**
     * Find an exception for a specific date in a rate period
     *
     * @param int $ratePeriodId
     * @param Carbon|string $date
     * @return RateException|null
     */
    public function findByDate(int $ratePeriodId, $date): ?RateException
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return RateException::where('rate_period_id', $ratePeriodId)
                           ->where('date', $date)
                           ->first();
    }

    /**
     * Create a new rate exception
     *
     * @param array $data
     * @return RateException
     */
    public function create(array $data): RateException
    {
        return RateException::create($data);
    }

    /**
     * Update a rate exception
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $exception = $this->find($id);
        
        if (!$exception) {
            return false;
        }
        
        return $exception->update($data);
    }

    /**
     * Delete a rate exception
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $exception = $this->find($id);
        
        if (!$exception) {
            return false;
        }
        
        return $exception->delete();
    }

    /**
     * Create or update an exception for a specific date
     *
     * @param int $ratePeriodId
     * @param Carbon|string $date
     * @param array $data
     * @return RateException
     */
    public function createOrUpdate(int $ratePeriodId, $date, array $data): RateException
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $exception = $this->findByDate($ratePeriodId, $date);
        
        if ($exception) {
            $exception->update($data);
            return $exception;
        }
        
        return $this->create(array_merge($data, [
            'rate_period_id' => $ratePeriodId,
            'date' => $date,
        ]));
    }

    /**
     * Get all exceptions for a date range in a rate period
     *
     * @param int $ratePeriodId
     * @param Carbon|string $startDate
     * @param Carbon|string $endDate
     * @return Collection
     */
    public function getExceptionsForDateRange(int $ratePeriodId, $startDate, $endDate): Collection
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);
        
        return RateException::where('rate_period_id', $ratePeriodId)
                           ->where('date', '>=', $startDate)
                           ->where('date', '<=', $endDate)
                           ->get();
    }
}