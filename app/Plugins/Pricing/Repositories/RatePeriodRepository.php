<?php

namespace App\Plugins\Pricing\Repositories;

use App\Plugins\Pricing\Models\RatePeriod;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RatePeriodRepository
{
    /**
     * Find a rate period by its ID
     *
     * @param int $id
     * @return RatePeriod|null
     */
    public function find(int $id): ?RatePeriod
    {
        return RatePeriod::find($id);
    }

    /**
     * Get all rate periods for a rate plan
     *
     * @param int $ratePlanId
     * @return Collection
     */
    public function getByRatePlan(int $ratePlanId): Collection
    {
        return RatePeriod::where('rate_plan_id', $ratePlanId)->get();
    }

    /**
     * Find periods that overlap with the given date range
     *
     * @param int $ratePlanId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $exceptPeriodId
     * @return Collection
     */
    public function findOverlappingPeriods(int $ratePlanId, Carbon $startDate, Carbon $endDate, ?int $exceptPeriodId = null): Collection
    {
        $query = RatePeriod::where('rate_plan_id', $ratePlanId)
                          ->overlapping($startDate, $endDate);
        
        if ($exceptPeriodId) {
            $query->where('id', '!=', $exceptPeriodId);
        }
        
        return $query->get();
    }

    /**
     * Find a period containing the specified date for a rate plan
     *
     * @param int $ratePlanId
     * @param Carbon|string $date
     * @return RatePeriod|null
     */
    public function findPeriodContainingDate(int $ratePlanId, $date): ?RatePeriod
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return RatePeriod::where('rate_plan_id', $ratePlanId)
                        ->containsDate($date)
                        ->first();
    }

    /**
     * Create a new rate period
     *
     * @param array $data
     * @return RatePeriod
     */
    public function create(array $data): RatePeriod
    {
        return RatePeriod::create($data);
    }

    /**
     * Update a rate period
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $period = $this->find($id);
        
        if (!$period) {
            return false;
        }
        
        return $period->update($data);
    }

    /**
     * Delete a rate period
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $period = $this->find($id);
        
        if (!$period) {
            return false;
        }
        
        return $period->delete();
    }

    /**
     * Get pricing data for a specific date range and rate plan
     *
     * @param int $ratePlanId
     * @param Carbon|string $startDate
     * @param Carbon|string $endDate
     * @return Collection
     */
    public function getPricingDataForDateRange(int $ratePlanId, $startDate, $endDate): Collection
    {
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);
        
        return RatePeriod::where('rate_plan_id', $ratePlanId)
                        ->when(method_exists(RatePeriod::class, 'overlapping'), function ($query) use ($startDate, $endDate) {
                            return $query->overlapping($startDate, $endDate);
                        })
                        ->with('rateExceptions')
                        ->get();
    }

    /**
     * Get active period for a date
     *
     * @param int $ratePlanId
     * @param Carbon|string $date
     * @return RatePeriod|null
     */
    public function getActivePeriodForDate(int $ratePlanId, $date): ?RatePeriod
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        
        return RatePeriod::where('rate_plan_id', $ratePlanId)
                        ->containsDate($date)
                        ->active()
                        ->first();
    }
}