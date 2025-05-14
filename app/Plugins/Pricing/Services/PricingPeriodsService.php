<?php

namespace App\Plugins\Pricing\Services;

use App\Plugins\Pricing\Models\RatePeriod;
use App\Plugins\Pricing\Models\RatePlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Period\Period;
use Spatie\Period\Precision;
use Spatie\Period\Boundaries;

class PricingPeriodsService
{
    /**
     * Creates an Spatie\Period from a RatePeriod model
     *
     * @param RatePeriod $ratePeriod
     * @return Period
     */
    public function createPeriodFromModel(RatePeriod $ratePeriod): Period
    {
        return Period::make(
            $ratePeriod->start_date,
            $ratePeriod->end_date,
            Precision::DAY,
            Boundaries::EXCLUDE_NONE
        );
    }

    /**
     * Check if periods overlap
     *
     * @param RatePeriod $period1
     * @param RatePeriod $period2
     * @return bool
     */
    public function periodsOverlap(RatePeriod $period1, RatePeriod $period2): bool
    {
        $period1Obj = $this->createPeriodFromModel($period1);
        $period2Obj = $this->createPeriodFromModel($period2);
        
        return $period1Obj->overlapsWith($period2Obj);
    }

    /**
     * Find the overlapping period between two periods
     *
     * @param RatePeriod $period1
     * @param RatePeriod $period2
     * @return Period|null
     */
    public function getOverlapPeriod(RatePeriod $period1, RatePeriod $period2): ?Period
    {
        $period1Obj = $this->createPeriodFromModel($period1);
        $period2Obj = $this->createPeriodFromModel($period2);
        
        if (!$period1Obj->overlapsWith($period2Obj)) {
            return null;
        }
        
        return $period1Obj->overlap($period2Obj);
    }

    /**
     * Check if one period completely contains another
     *
     * @param RatePeriod $outerPeriod
     * @param RatePeriod $innerPeriod
     * @return bool
     */
    public function periodContains(RatePeriod $outerPeriod, RatePeriod $innerPeriod): bool
    {
        $outerPeriodObj = $this->createPeriodFromModel($outerPeriod);
        $innerPeriodObj = $this->createPeriodFromModel($innerPeriod);
        
        return $outerPeriodObj->contains($innerPeriodObj);
    }

    /**
     * Get the gap between two periods, if any
     *
     * @param RatePeriod $period1
     * @param RatePeriod $period2
     * @return Period|null
     */
    public function getGapBetweenPeriods(RatePeriod $period1, RatePeriod $period2): ?Period
    {
        $period1Obj = $this->createPeriodFromModel($period1);
        $period2Obj = $this->createPeriodFromModel($period2);
        
        if ($period1Obj->touchesWith($period2Obj) || $period1Obj->overlapsWith($period2Obj)) {
            return null;
        }
        
        // Determine which period comes first
        if ($period1Obj->startsAt() < $period2Obj->startsAt()) {
            return Period::make(
                $period1Obj->endsAt()->addDay(),
                $period2Obj->startsAt()->subDay(),
                Precision::DAY
            );
        } else {
            return Period::make(
                $period2Obj->endsAt()->addDay(),
                $period1Obj->startsAt()->subDay(),
                Precision::DAY
            );
        }
    }

    /**
     * Split a period at a given date
     *
     * @param RatePeriod $period
     * @param Carbon $splitDate
     * @return array Two new periods
     */
    public function splitPeriodAtDate(RatePeriod $period, Carbon $splitDate): array
    {
        $periodObj = $this->createPeriodFromModel($period);
        
        // Check if the split date is within the period
        if (!$periodObj->contains(Period::make($splitDate, $splitDate, Precision::DAY))) {
            Log::warning('Attempted to split period at date outside of period', [
                'period_id' => $period->id,
                'period_start' => $period->start_date->format('Y-m-d'),
                'period_end' => $period->end_date->format('Y-m-d'),
                'split_date' => $splitDate->format('Y-m-d')
            ]);
            return [$period]; // Return original period if split date is outside
        }
        
        // Create two new periods
        $firstPeriod = Period::make(
            $periodObj->startsAt(),
            $splitDate->copy()->subDay(),
            Precision::DAY
        );
        
        $secondPeriod = Period::make(
            $splitDate,
            $periodObj->endsAt(),
            Precision::DAY
        );
        
        return [$firstPeriod, $secondPeriod];
    }

    /**
     * Create multiple periods based on a new period and existing ones
     * This handles all overlap scenarios automatically
     *
     * @param int $ratePlanId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param Collection $existingPeriods
     * @param array $periodData Additional data for the new period
     * @return array Created/updated periods
     */
    public function createOptimizedPeriods(
        int $ratePlanId,
        Carbon $startDate,
        Carbon $endDate,
        Collection $existingPeriods,
        array $periodData
    ): array {
        $newPeriod = Period::make($startDate, $endDate, Precision::DAY);
        $results = [];
        
        // If no existing periods, create a single new one
        if ($existingPeriods->isEmpty()) {
            $newRatePeriod = RatePeriod::create(array_merge($periodData, [
                'rate_plan_id' => $ratePlanId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]));
            
            $results[] = $newRatePeriod;
            return $results;
        }
        
        // Convert existing periods to Period objects for analysis
        $existingPeriodObjects = $existingPeriods->map(function ($period) {
            return [
                'model' => $period,
                'period' => $this->createPeriodFromModel($period)
            ];
        });
        
        // Check for completely overlapping periods that should be updated instead of created
        $completeOverlaps = $existingPeriodObjects->filter(function ($item) use ($newPeriod) {
            return $item['period']->equals($newPeriod);
        });
        
        if ($completeOverlaps->isNotEmpty()) {
            // Update the existing period with new data
            $periodToUpdate = $completeOverlaps->first()['model'];
            $periodToUpdate->update($periodData);
            $results[] = $periodToUpdate;
            return $results;
        }
        
        // Collect all periods that overlap with our new period
        $overlappingPeriods = $existingPeriodObjects->filter(function ($item) use ($newPeriod) {
            return $item['period']->overlapsWith($newPeriod);
        });
        
        // If no overlapping periods, create the new one
        if ($overlappingPeriods->isEmpty()) {
            $newRatePeriod = RatePeriod::create(array_merge($periodData, [
                'rate_plan_id' => $ratePlanId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]));
            
            $results[] = $newRatePeriod;
            return $results;
        }
        
        // Handle complex overlapping scenarios
        // First, gather all boundaries to identify distinct periods we need to create
        $boundaries = collect([$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        $overlappingPeriods->each(function ($item) use (&$boundaries) {
            $boundaries->push($item['model']->start_date->format('Y-m-d'));
            $boundaries->push($item['model']->end_date->format('Y-m-d'));
        });
        
        // Sort boundaries and remove duplicates
        $boundaries = $boundaries->unique()->sort();
        
        // Create segments between each pair of adjacent boundaries
        $segments = collect();
        for ($i = 0; $i < $boundaries->count() - 1; $i++) {
            $segmentStart = Carbon::parse($boundaries[$i]);
            $segmentEnd = Carbon::parse($boundaries[$i + 1])->subDay();
            
            // Skip invalid segments (end before start)
            if ($segmentEnd < $segmentStart) {
                continue;
            }
            
            $segmentPeriod = Period::make($segmentStart, $segmentEnd, Precision::DAY);
            
            // Only include segments that are within our new period
            if ($newPeriod->contains($segmentPeriod) || $newPeriod->overlapsWith($segmentPeriod)) {
                $segments->push([
                    'start' => $segmentStart,
                    'end' => $segmentEnd,
                    'period' => $segmentPeriod
                ]);
            }
        }
        
        // Now process each segment
        foreach ($segments as $segment) {
            // Check if this segment is within our new period
            $segmentInNewPeriod = $newPeriod->contains($segment['period']);
            
            if ($segmentInNewPeriod) {
                // Delete any existing periods that completely overlap with this segment
                $overlappingForSegment = $overlappingPeriods->filter(function ($item) use ($segment) {
                    return $item['period']->contains($segment['period']);
                });
                
                foreach ($overlappingForSegment as $item) {
                    $item['model']->delete();
                }
                
                // Create a new period for this segment with our data
                $newRatePeriod = RatePeriod::create(array_merge($periodData, [
                    'rate_plan_id' => $ratePlanId,
                    'start_date' => $segment['start'],
                    'end_date' => $segment['end']
                ]));
                
                $results[] = $newRatePeriod;
            }
        }
        
        // Handle any remaining existing periods that need modification
        foreach ($overlappingPeriods as $item) {
            $existingPeriod = $item['model'];
            $existingPeriodObj = $item['period'];
            
            // Check if this existing period extends beyond our new period
            if ($existingPeriodObj->startsAt() < $newPeriod->startsAt()) {
                // Create a new period for the part before our new period
                $beforePeriod = RatePeriod::create([
                    'rate_plan_id' => $ratePlanId,
                    'start_date' => $existingPeriod->start_date,
                    'end_date' => $startDate->copy()->subDay(),
                    'base_price' => $existingPeriod->base_price,
                    'prices' => $existingPeriod->prices,
                    'min_stay' => $existingPeriod->min_stay,
                    'quantity' => $existingPeriod->quantity,
                    'sales_type' => $existingPeriod->sales_type,
                    'status' => $existingPeriod->status
                ]);
                
                $results[] = $beforePeriod;
            }
            
            if ($existingPeriodObj->endsAt() > $newPeriod->endsAt()) {
                // Create a new period for the part after our new period
                $afterPeriod = RatePeriod::create([
                    'rate_plan_id' => $ratePlanId,
                    'start_date' => $endDate->copy()->addDay(),
                    'end_date' => $existingPeriod->end_date,
                    'base_price' => $existingPeriod->base_price,
                    'prices' => $existingPeriod->prices,
                    'min_stay' => $existingPeriod->min_stay,
                    'quantity' => $existingPeriod->quantity,
                    'sales_type' => $existingPeriod->sales_type,
                    'status' => $existingPeriod->status
                ]);
                
                $results[] = $afterPeriod;
            }
            
            // Delete the original existing period if it's completely handled by our new segments
            $existingPeriod->delete();
        }
        
        return $results;
    }

    /**
     * Merge adjacent periods with the same properties
     *
     * @param int $ratePlanId
     * @return int Number of periods merged
     */
    public function optimizePeriodsForRatePlan(int $ratePlanId): int
    {
        $ratePlan = RatePlan::findOrFail($ratePlanId);
        $allPeriods = $ratePlan->ratePeriods()->orderBy('start_date')->get();
        
        if ($allPeriods->count() <= 1) {
            return 0; // Nothing to merge
        }
        
        $mergeCount = 0;
        $previousPeriod = null;
        
        foreach ($allPeriods as $currentPeriod) {
            if ($previousPeriod === null) {
                $previousPeriod = $currentPeriod;
                continue;
            }
            
            // Check if periods are adjacent and have the same properties
            $isAdjacent = $previousPeriod->end_date->addDay()->isSameDay($currentPeriod->start_date);
            
            $hasSameProperties = 
                $previousPeriod->base_price == $currentPeriod->base_price &&
                $previousPeriod->min_stay == $currentPeriod->min_stay &&
                $previousPeriod->quantity == $currentPeriod->quantity &&
                $previousPeriod->sales_type == $currentPeriod->sales_type &&
                $previousPeriod->status == $currentPeriod->status &&
                json_encode($previousPeriod->prices) == json_encode($currentPeriod->prices);
            
            if ($isAdjacent && $hasSameProperties) {
                // Merge the periods
                $previousPeriod->end_date = $currentPeriod->end_date;
                $previousPeriod->save();
                
                // Move any exceptions from the current period to the previous period
                $currentPeriod->rateExceptions()->update(['rate_period_id' => $previousPeriod->id]);
                
                // Delete the current period
                $currentPeriod->delete();
                
                $mergeCount++;
                // Don't update previousPeriod here as it remains the same for the next comparison
            } else {
                $previousPeriod = $currentPeriod;
            }
        }
        
        return $mergeCount;
    }
}