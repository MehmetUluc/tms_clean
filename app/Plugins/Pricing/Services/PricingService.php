<?php

namespace App\Plugins\Pricing\Services;

use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Pricing\Models\BookingPrice;
use App\Plugins\Pricing\Models\RateException;
use App\Plugins\Pricing\Models\RatePeriod;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Repositories\RatePlanRepository;
use App\Plugins\Pricing\Repositories\RatePeriodRepository;
use App\Plugins\Pricing\Repositories\RateExceptionRepository;
use App\Plugins\Pricing\Repositories\BookingPriceRepository;
use App\Plugins\Pricing\Services\PricingPeriodsService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingService
{
    protected RatePlanRepository $ratePlanRepository;
    protected RatePeriodRepository $ratePeriodRepository;
    protected RateExceptionRepository $rateExceptionRepository;
    protected BookingPriceRepository $bookingPriceRepository;

    /**
     * Get the rate plan repository
     *
     * @return RatePlanRepository
     */
    public function getRatePlanRepository(): RatePlanRepository
    {
        return $this->ratePlanRepository;
    }

    /**
     * Get the rate period repository
     *
     * @return RatePeriodRepository
     */
    public function getRatePeriodRepository(): RatePeriodRepository
    {
        return $this->ratePeriodRepository;
    }

    /**
     * Get the rate exception repository
     *
     * @return RateExceptionRepository
     */
    public function getRateExceptionRepository(): RateExceptionRepository
    {
        return $this->rateExceptionRepository;
    }

    /**
     * Get the booking price repository
     *
     * @return BookingPriceRepository
     */
    public function getBookingPriceRepository(): BookingPriceRepository
    {
        return $this->bookingPriceRepository;
    }

    /**
     * Constructor
     *
     * @param RatePlanRepository $ratePlanRepository
     * @param RatePeriodRepository $ratePeriodRepository
     * @param RateExceptionRepository $rateExceptionRepository
     * @param BookingPriceRepository $bookingPriceRepository
     */
    public function __construct(
        RatePlanRepository $ratePlanRepository,
        RatePeriodRepository $ratePeriodRepository,
        RateExceptionRepository $rateExceptionRepository,
        BookingPriceRepository $bookingPriceRepository
    ) {
        $this->ratePlanRepository = $ratePlanRepository;
        $this->ratePeriodRepository = $ratePeriodRepository;
        $this->rateExceptionRepository = $rateExceptionRepository;
        $this->bookingPriceRepository = $bookingPriceRepository;
    }
    /**
     * Get rate plans for a specific hotel
     *
     * @param int $hotelId
     * @return Collection
     */
    public function getRatePlansForHotel(int $hotelId): Collection
    {
        return $this->ratePlanRepository->getByHotel($hotelId);
    }

    /**
     * Get rate plans for a specific room
     *
     * @param int $roomId
     * @return Collection
     */
    public function getRatePlansForRoom(int $roomId): Collection
    {
        return $this->ratePlanRepository->getByRoom($roomId);
    }

    /**
     * Create a new rate plan
     *
     * @param array $data
     * @return RatePlan
     */
    public function createRatePlan(array $data): RatePlan
    {
        // Check if a plan already exists for this combination
        $existingPlan = $this->ratePlanRepository->findByHotelRoomAndBoardType(
            $data['hotel_id'],
            $data['room_id'],
            $data['board_type_id']
        );

        if ($existingPlan) {
            throw new Exception("A rate plan already exists for this room and board type combination.");
        }

        return $this->ratePlanRepository->create($data);
    }

    /**
     * Create or update a rate period, ensuring no overlapping periods
     * Uses Spatie\Period to handle all date range logic
     *
     * @param int $ratePlanId
     * @param array $data
     * @return RatePeriod
     */
    public function createOrUpdateRatePeriod(int $ratePlanId, array $data): RatePeriod
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        // Use the Spatie/Period method for more accurate overlap detection
        $overlappingPeriods = $this->findOverlappingPeriodsWithSpatie(
            $ratePlanId,
            $startDate,
            $endDate,
            $data['id'] ?? null
        );

        // Start a database transaction to ensure data integrity
        return DB::transaction(function() use ($ratePlanId, $data, $startDate, $endDate, $overlappingPeriods) {
            // Handle overlapping periods, whether creating new or updating
            if ($overlappingPeriods->isNotEmpty()) {
                $this->handleOverlappingPeriods($data['id'] ?? null, $overlappingPeriods, $startDate, $endDate);
            }

            // Prepare the data
            $periodData = array_merge($data, [
                'rate_plan_id' => $ratePlanId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Ensure sales_type is properly cast as string
            if (isset($periodData['sales_type'])) {
                $periodData['sales_type'] = (string)$periodData['sales_type'];
            }

            // Ensure base_price is never null
            if (!isset($periodData['base_price']) || $periodData['base_price'] === null) {
                $periodData['base_price'] = 0;
            } else {
                // Convert to float to avoid type issues
                $periodData['base_price'] = (float)$periodData['base_price'];
            }

            // Make sure prices is proper JSON
            if (isset($periodData['prices']) && !is_null($periodData['prices'])) {
                if (is_array($periodData['prices']) && empty($periodData['prices'])) {
                    $periodData['prices'] = null;
                }
            }

            // Check if we're updating or creating a new period
            if (isset($data['id'])) {
                $period = RatePeriod::findOrFail($data['id']);
                $period->update($periodData);
            } else {
                // Before creating, check for exact matches to avoid duplicates
                $existingExactMatch = RatePeriod::where('rate_plan_id', $ratePlanId)
                    ->where('start_date', $startDate)
                    ->where('end_date', $endDate)
                    ->first();

                if ($existingExactMatch) {
                    // Update the existing exact match instead of creating a new one
                    $existingExactMatch->update($periodData);
                    $period = $existingExactMatch;
                    // Updated existing exact match period instead of creating new
                } else {
                    // Create a new period
                    $period = RatePeriod::create($periodData);
                    // Created new period
                }
            }

            // After creating or updating, check if we can optimize by merging adjacent periods
            // This is an optional step that can help keep the database clean
            app(PricingPeriodsService::class)->optimizePeriodsForRatePlan($ratePlanId);

            return $period;
        });
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
        return $this->ratePeriodRepository->findOverlappingPeriods($ratePlanId, $startDate, $endDate, $exceptPeriodId);
    }

    /**
     * Find periods that overlap with the given date range using Spatie/Period
     * This provides more accurate and reliable overlap detection
     *
     * @param int $ratePlanId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int|null $exceptPeriodId
     * @return Collection
     */
    public function findOverlappingPeriodsWithSpatie(int $ratePlanId, Carbon $startDate, Carbon $endDate, ?int $exceptPeriodId = null): Collection
    {
        // Create a Spatie\Period object for the target date range
        $targetPeriod = \Spatie\Period\Period::make(
            $startDate,
            $endDate,
            \Spatie\Period\Precision::DAY,
            \Spatie\Period\Boundaries::EXCLUDE_NONE
        );

        // Get all periods for this rate plan
        $allPeriods = $this->ratePeriodRepository->getByRatePlan($ratePlanId);

        if ($exceptPeriodId) {
            $allPeriods = $allPeriods->filter(function ($period) use ($exceptPeriodId) {
                return $period->id != $exceptPeriodId;
            });
        }

        // Filter for overlapping periods using Spatie/Period
        return $allPeriods->filter(function ($period) use ($targetPeriod) {
            $periodObj = \Spatie\Period\Period::make(
                $period->start_date,
                $period->end_date,
                \Spatie\Period\Precision::DAY,
                \Spatie\Period\Boundaries::EXCLUDE_NONE
            );

            return $periodObj->overlapsWith($targetPeriod);
        });
    }

    /**
     * Handle overlapping periods by adjusting their date ranges
     *
     * @param int|null $currentPeriodId
     * @param Collection $overlappingPeriods
     * @param Carbon $newStartDate
     * @param Carbon $newEndDate
     * @return void
     */
    protected function handleOverlappingPeriods(?int $currentPeriodId, Collection $overlappingPeriods, Carbon $newStartDate, Carbon $newEndDate): void
    {
        // Handling overlapping periods

        // Create the new period object using Spatie/Period
        $newPeriodObj = \Spatie\Period\Period::make(
            $newStartDate,
            $newEndDate,
            \Spatie\Period\Precision::DAY,
            \Spatie\Period\Boundaries::EXCLUDE_NONE
        );

        $totalPeriods = $overlappingPeriods->count();
        $processedPeriods = 0;

        foreach ($overlappingPeriods as $period) {
            // Skip if this is the current period being updated
            if ($currentPeriodId && $period->id == $currentPeriodId) {
                \Log::info('Skipping current period', ['periodId' => $period->id]);
                continue;
            }

            // Create Spatie Period object for the current period
            $periodObj = \Spatie\Period\Period::make(
                $period->start_date,
                $period->end_date,
                \Spatie\Period\Precision::DAY,
                \Spatie\Period\Boundaries::EXCLUDE_NONE
            );

            $periodStart = $period->start_date->format('Y-m-d');
            $periodEnd = $period->end_date->format('Y-m-d');

            // Get the relationship between the periods using Spatie/Period
            $isContained = $newPeriodObj->contains($periodObj);
            $overlaps = $newPeriodObj->overlapsWith($periodObj);
            $touches = $newPeriodObj->touchesWith($periodObj);

            // Case 1: Period completely contained within new range - delete it
            if ($isContained) {
                // Case 1: Deleting completely contained period
                $period->delete();
                $processedPeriods++;
                continue;
            }

            // If periods overlap, handle the different overlap scenarios
            if ($overlaps) {
                // Get the overlap between the periods
                $overlap = $periodObj->overlap($newPeriodObj);

                // Case 2: Period starts before new range and overlaps with it
                if ($periodObj->startsAt() < $newPeriodObj->startsAt() &&
                    $periodObj->endsAt() >= $newPeriodObj->startsAt() &&
                    $periodObj->endsAt() <= $newPeriodObj->endsAt()) {

                    // Case 2: Trimming end date of overlapping period

                    $period->update(['end_date' => $newStartDate->copy()->subDay()]);
                    $processedPeriods++;
                    continue;
                }

                // Case 3: Period starts within new range and ends after it
                if ($periodObj->startsAt() >= $newPeriodObj->startsAt() &&
                    $periodObj->startsAt() <= $newPeriodObj->endsAt() &&
                    $periodObj->endsAt() > $newPeriodObj->endsAt()) {

                    // Case 3: Adjusting start date of overlapping period

                    $period->update(['start_date' => $newEndDate->copy()->addDay()]);
                    $processedPeriods++;
                    continue;
                }

                // Case 4: Period spans the entire new range (starts before and ends after)
                if ($periodObj->startsAt() < $newPeriodObj->startsAt() &&
                    $periodObj->endsAt() > $newPeriodObj->endsAt()) {

                    // Case 4: Splitting period that spans the entire range

                    // Split into two periods using Spatie/Period's subtraction
                    $remainingPeriods = $periodObj->subtract($newPeriodObj);

                    // There should be exactly two remaining periods (before and after)
                    if (count($remainingPeriods) === 2) {
                        // Update the original period with the first remaining period
                        $firstPeriod = $remainingPeriods[0];
                        $period->update([
                            'start_date' => Carbon::parse($firstPeriod->startsAt()),
                            'end_date' => Carbon::parse($firstPeriod->endsAt())
                        ]);

                        // Create a new period for the second remaining period
                        $secondPeriod = $remainingPeriods[1];
                        $newPeriod = $period->replicate();
                        $newPeriod->start_date = Carbon::parse($secondPeriod->startsAt());
                        $newPeriod->end_date = Carbon::parse($secondPeriod->endsAt());
                        $newPeriod->save();

                        // Split result complete
                    } else {
                        // Fallback to the original method if something unexpected happens
                        $newPeriod = $period->replicate();
                        $period->update(['end_date' => $newStartDate->copy()->subDay()]);
                        $newPeriod->start_date = $newEndDate->copy()->addDay();
                        $newPeriod->save();

                        // Split result (fallback method) complete
                    }

                    $processedPeriods++;
                }
            }
        }

        // Overlap handling complete
    }

    /**
     * Create a daily rate exception
     *
     * @param int $ratePeriodId
     * @param array $data
     * @return RateException
     */
    public function createRateException(int $ratePeriodId, array $data): RateException
    {
        $period = $this->ratePeriodRepository->find($ratePeriodId);
        if (!$period) {
            throw new Exception("Rate period not found.");
        }

        $date = Carbon::parse($data['date']);

        // Check if date is within period range
        if ($date->lt($period->start_date) || $date->gt($period->end_date)) {
            throw new Exception("Exception date must be within the period's date range.");
        }

        // Check if exception already exists
        $existingException = $this->rateExceptionRepository->findByDate($ratePeriodId, $date);

        // Ensure proper data types
        $preparedData = $data;
        if (isset($preparedData['sales_type'])) {
            $preparedData['sales_type'] = (string)$preparedData['sales_type'];
        }

        // Get the rate plan for this period
        $ratePlan = $period->ratePlan;

        // Always ensure base_price is never null for any pricing type
        if (!isset($preparedData['base_price']) || $preparedData['base_price'] === null) {
            $preparedData['base_price'] = $period->base_price ?? 0;
        } else {
            // Convert to float to avoid type issues
            $preparedData['base_price'] = (float)$preparedData['base_price'];
        }

        // For non per-person pricing, ensure prices is null
        if (!$ratePlan->is_per_person) {
            $preparedData['prices'] = null;
        } else {
            // For per-person pricing, handle empty prices array
            if (empty($preparedData['prices']) || !is_array($preparedData['prices'])) {
                // Double-check base_price is set when prices is empty
                if ($preparedData['base_price'] === null) {
                    $preparedData['base_price'] = $period->base_price ?? 0;
                }
            }
        }

        if ($existingException) {
            // Update existing exception
            $this->rateExceptionRepository->update($existingException->id, $preparedData);
            return $this->rateExceptionRepository->find($existingException->id);
        }

        // Create new exception
        return $this->rateExceptionRepository->create(array_merge($preparedData, [
            'rate_period_id' => $ratePeriodId,
            'date' => $date,
        ]));
    }

    /**
     * Delete a rate exception
     *
     * @param int $exceptionId
     * @return bool
     */
    public function deleteRateException(int $exceptionId): bool
    {
        return $this->rateExceptionRepository->delete($exceptionId);
    }

    /**
     * Get pricing data for a specific date range and rate plan
     *
     * @param int $ratePlanId
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @return array
     */
    public function getPricingDataForDateRange(int $ratePlanId, $startDate, $endDate): array
    {
        try {
            $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
            $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

            $ratePlan = $this->ratePlanRepository->find($ratePlanId);
            if (!$ratePlan) {
                throw new Exception("Rate plan not found.");
            }

            // First try to load from DailyRates (new system)
            $dailyRateService = app(DailyRateService::class);

            try {
                $dailyRates = $dailyRateService->getDailyRates(
                    $ratePlanId,
                    $startDate->format('Y-m-d'),
                    $endDate->format('Y-m-d')
                );

                // If we have daily rates, use them
                if ($dailyRates->isNotEmpty()) {
                    $result = [];
                    foreach ($dailyRates as $rate) {
                        $dateStr = $rate->date->format('Y-m-d');

                        // Convert prices_json to prices array if needed
                        $prices = null;
                        if ($rate->is_per_person && $rate->prices_json) {
                            try {
                                if (is_string($rate->prices_json)) {
                                    $prices = json_decode($rate->prices_json, true);
                                } elseif (is_array($rate->prices_json)) {
                                    $prices = $rate->prices_json;
                                }
                            } catch (\Exception $e) {
                                \Log::warning('Failed to decode prices_json: ' . $e->getMessage(), [
                                    'rate_plan_id' => $ratePlanId,
                                    'date' => $dateStr,
                                    'prices_json' => $rate->prices_json
                                ]);
                            }
                        }

                        // Create pricing data from daily rate
                        $result[$dateStr] = [
                            'has_pricing' => true,
                            'base_price' => $rate->base_price,
                            'prices' => $prices, // Use decoded prices
                            'prices_json' => $rate->prices_json, // Also include the raw prices_json
                            'min_stay' => $rate->min_stay_arrival,
                            'quantity' => 1, // Default quantity
                            'sales_type' => $rate->sales_type,
                            'status' => $rate->status === 'available',
                            'is_exception' => false,
                            'is_refundable' => $rate->is_refundable,
                            'is_per_person' => $rate->is_per_person,
                        ];
                    }

                    \Log::debug('Loaded pricing data from daily rates', [
                        'rate_plan_id' => $ratePlanId,
                        'dates_count' => count($result),
                        'first_date' => array_key_first($result) ?? null
                    ]);

                    return $result;
                }
            } catch (\Exception $e) {
                \Log::warning('Error loading from daily rates, falling back to periods: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Fallback to old periods-based system if no daily rates found
            $periods = $this->ratePeriodRepository->getPricingDataForDateRange($ratePlanId, $startDate, $endDate);

            $result = [];
            $dateRange = CarbonPeriod::create($startDate, $endDate);

            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $pricingData = $this->getPricingDataForDate($ratePlan, $periods, $date);
                $result[$dateStr] = $pricingData;
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('Error getting pricing data: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get pricing data for a specific date
     * 
     * @param RatePlan $ratePlan
     * @param Collection $periods
     * @param Carbon $date
     * @return array
     */
    protected function getPricingDataForDate(RatePlan $ratePlan, Collection $periods, Carbon $date): array
    {
        // Find the period that contains this date
        $period = $periods->first(function ($period) use ($date) {
            return $date->between($period->start_date, $period->end_date);
        });
        
        // If no period found, return empty data
        if (!$period) {
            return [
                'has_pricing' => false,
                'base_price' => null,
                'prices' => null,
                'min_stay' => null,
                'quantity' => null,
                'sales_type' => null,
                'status' => false,
                'is_exception' => false,
            ];
        }
        
        // Check for exceptions
        $exception = $period->rateExceptions->first(function ($exception) use ($date) {
            return $exception->date->isSameDay($date);
        });
        
        // If exception found, use its data, otherwise use period data
        if ($exception) {
            return [
                'has_pricing' => true,
                'base_price' => $exception->base_price ?? $period->base_price,
                'prices' => $exception->prices ?? $period->prices,
                'min_stay' => $exception->min_stay ?? $period->min_stay,
                'quantity' => $exception->quantity ?? $period->quantity,
                'sales_type' => $exception->sales_type ?? $period->sales_type,
                'status' => $exception->status ?? $period->status,
                'is_exception' => true,
                'exception_id' => $exception->id,
            ];
        }
        
        return [
            'has_pricing' => true,
            'base_price' => $period->base_price,
            'prices' => $period->prices,
            'min_stay' => $period->min_stay,
            'quantity' => $period->quantity,
            'sales_type' => $period->sales_type,
            'status' => $period->status,
            'is_exception' => false,
        ];
    }

    /**
     * Calculate price for a specific date, rate plan, and occupancy
     *
     * @param int $ratePlanId
     * @param string|Carbon $date
     * @param int $occupancy
     * @return array
     */
    public function calculatePriceForDate(int $ratePlanId, $date, int $occupancy = 1): array
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        $ratePlan = $this->ratePlanRepository->find($ratePlanId);
        if (!$ratePlan) {
            throw new Exception("Rate plan not found.");
        }

        $period = $this->ratePeriodRepository->getActivePeriodForDate($ratePlanId, $date);
        
        if (!$period) {
            return [
                'available' => false,
                'price' => null,
                'reason' => 'no_period',
            ];
        }
        
        // Check for exception
        $exception = $period->rateExceptions()
                          ->onDate($date)
                          ->first();
        
        $status = $exception ? ($exception->status ?? $period->status) : $period->status;
        
        if (!$status) {
            return [
                'available' => false,
                'price' => null,
                'reason' => 'not_active',
            ];
        }
        
        $quantity = $exception ? ($exception->quantity ?? $period->quantity) : $period->quantity;
        
        if ($quantity <= 0) {
            return [
                'available' => false,
                'price' => null,
                'reason' => 'no_stock',
            ];
        }
        
        $salesType = $exception ? ($exception->sales_type ?? $period->sales_type) : $period->sales_type;
        
        // If per person pricing
        if ($ratePlan->is_per_person) {
            $prices = $exception ? ($exception->prices ?? $period->prices) : $period->prices;
            
            if (!isset($prices[$occupancy])) {
                return [
                    'available' => false,
                    'price' => null,
                    'reason' => 'no_price_for_occupancy',
                ];
            }
            
            $price = $prices[$occupancy];
        } else {
            // Unit based pricing
            $price = $exception ? ($exception->base_price ?? $period->base_price) : $period->base_price;
        }
        
        if ($price === null) {
            return [
                'available' => false,
                'price' => null,
                'reason' => 'no_price',
            ];
        }
        
        $minStay = $exception ? ($exception->min_stay ?? $period->min_stay) : $period->min_stay;
        
        return [
            'available' => true,
            'price' => $price,
            'min_stay' => $minStay,
            'quantity' => $quantity,
            'sales_type' => $salesType,
            'is_per_person' => $ratePlan->is_per_person,
        ];
    }

    /**
     * Calculate prices for a reservation date range
     * 
     * @param int $ratePlanId
     * @param string|Carbon $checkIn
     * @param string|Carbon $checkOut
     * @param int $occupancy
     * @return array
     */
    public function calculatePricesForStay(int $ratePlanId, $checkIn, $checkOut, int $occupancy = 1): array
    {
        $checkIn = $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn);
        $checkOut = $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut);
        
        $nights = $checkIn->diffInDays($checkOut);
        
        if ($nights < 1) {
            throw new Exception("Check-out date must be after check-in date.");
        }
        
        $ratePlan = RatePlan::findOrFail($ratePlanId);
        
        $result = [
            'available' => true,
            'prices' => [],
            'total_price' => 0,
            'min_stay_violation' => false,
            'rate_plan' => $ratePlan,
        ];
        
        $dateRange = CarbonPeriod::create($checkIn, $checkOut->copy()->subDay());
        
        foreach ($dateRange as $date) {
            $priceData = $this->calculatePriceForDate($ratePlanId, $date, $occupancy);
            
            if (!$priceData['available']) {
                $result['available'] = false;
                $result['unavailable_reason'] = $priceData['reason'];
                $result['unavailable_date'] = $date->format('Y-m-d');
                return $result;
            }
            
            if ($nights < $priceData['min_stay']) {
                $result['min_stay_violation'] = true;
                $result['required_min_stay'] = max($result['required_min_stay'] ?? 0, $priceData['min_stay']);
            }
            
            $result['prices'][$date->format('Y-m-d')] = $priceData;
            $result['total_price'] += $priceData['price'];
        }
        
        // If total stay is less than minimum required, mark as unavailable
        if ($result['min_stay_violation']) {
            $result['available'] = false;
            $result['unavailable_reason'] = 'min_stay_not_met';
        }
        
        return $result;
    }

    /**
     * Create price snapshots for a reservation
     *
     * @param Reservation $reservation
     * @param int $ratePlanId
     * @param int $occupancy
     * @return array
     */
    public function createReservationPriceSnapshots(Reservation $reservation, int $ratePlanId, int $occupancy = 1): array
    {
        $ratePlan = $this->ratePlanRepository->find($ratePlanId);
        if (!$ratePlan) {
            throw new Exception("Rate plan not found.");
        }

        $checkIn = $reservation->check_in;
        $checkOut = $reservation->check_out;

        $priceData = $this->calculatePricesForStay($ratePlanId, $checkIn, $checkOut, $occupancy);

        if (!$priceData['available']) {
            throw new Exception("Cannot create reservation: " . $priceData['unavailable_reason']);
        }

        // Start transaction to ensure all prices are created
        DB::beginTransaction();

        try {
            // Delete any existing price snapshots for this reservation
            $this->bookingPriceRepository->deleteByReservation($reservation->id);

            // Create new price snapshots
            $snapshotsData = [];

            foreach ($priceData['prices'] as $date => $price) {
                $snapshotsData[] = [
                    'reservation_id' => $reservation->id,
                    'rate_plan_id' => $ratePlanId,
                    'date' => $date,
                    'price' => $price['price'],
                    'guests_count' => $occupancy,
                    'is_per_person' => $ratePlan->is_per_person,
                    'original_data' => $price,
                ];
            }

            $snapshots = $this->bookingPriceRepository->createMany($snapshotsData);

            DB::commit();

            return [
                'success' => true,
                'snapshots' => $snapshots,
                'total_price' => $priceData['total_price'],
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating price snapshots: ' . $e->getMessage());

            throw new Exception("Failed to create price snapshots: " . $e->getMessage());
        }
    }

    /**
     * Update inventory/quantity when a reservation is confirmed
     * 
     * @param Reservation $reservation
     * @return bool
     */
    public function updateInventoryForReservation(Reservation $reservation): bool
    {
        // Get the booking prices for this reservation
        $bookingPrices = BookingPrice::where('reservation_id', $reservation->id)->get();
        
        if ($bookingPrices->isEmpty()) {
            return false;
        }
        
        $ratePlanId = $bookingPrices->first()->rate_plan_id;
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            foreach ($bookingPrices as $bookingPrice) {
                $date = $bookingPrice->date;
                
                // Find the period that contains this date
                $period = RatePeriod::where('rate_plan_id', $ratePlanId)
                                  ->containsDate($date)
                                  ->first();
                
                if (!$period) {
                    continue;
                }
                
                // Check for exception
                $exception = $period->rateExceptions()
                                  ->onDate($date)
                                  ->first();
                
                if ($exception && $exception->quantity !== null) {
                    // Update exception quantity
                    if ($exception->quantity > 0) {
                        $exception->quantity -= 1;
                        $exception->save();
                    }
                } else {
                    // Update period quantity
                    if ($period->quantity > 0) {
                        $period->quantity -= 1;
                        $period->save();
                    }
                }
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating inventory: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restore inventory/quantity when a reservation is cancelled
     * 
     * @param Reservation $reservation
     * @return bool
     */
    public function restoreInventoryForReservation(Reservation $reservation): bool
    {
        // Get the booking prices for this reservation
        $bookingPrices = BookingPrice::where('reservation_id', $reservation->id)->get();

        if ($bookingPrices->isEmpty()) {
            return false;
        }

        $ratePlanId = $bookingPrices->first()->rate_plan_id;

        // Start transaction
        DB::beginTransaction();

        try {
            foreach ($bookingPrices as $bookingPrice) {
                $date = $bookingPrice->date;

                // Find the period that contains this date
                $period = RatePeriod::where('rate_plan_id', $ratePlanId)
                                  ->containsDate($date)
                                  ->first();

                if (!$period) {
                    continue;
                }

                // Check for exception
                $exception = $period->rateExceptions()
                                  ->onDate($date)
                                  ->first();

                if ($exception && $exception->quantity !== null) {
                    // Update exception quantity
                    $exception->quantity += 1;
                    $exception->save();
                } else {
                    // Update period quantity
                    $period->quantity += 1;
                    $period->save();
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error restoring inventory: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Advanced pricing management functions
     * Added for service-based approach, extracted from UI components
     */

    /**
     * Save pricing data for multiple date ranges and rate plans
     *
     * @param array $ratePlans Rate plan data
     * @param array $formData Form data with pricing information
     * @param array $dateRange Date range to process
     * @return array Success status and messages
     */
    public function savePricingData(array $ratePlans, array $formData, array $dateRange): array
    {
        $results = [
            'success' => true,
            'message' => 'Fiyatlar başarıyla kaydedildi.',
            'updatedPeriods' => 0,
            'updatedExceptions' => 0,
            'errors' => [],
        ];

        // Log incoming data summary
        \Log::info('Starting pricing data save operation', [
            'ratePlanCount' => count($ratePlans),
            'dateRangeCount' => count($dateRange),
            'firstDate' => $dateRange[0] ?? 'none',
            'lastDate' => $dateRange[count($dateRange) - 1] ?? 'none',
        ]);

        try {
            DB::beginTransaction();

            // Process each rate plan
            foreach ($ratePlans as $ratePlanId => $ratePlan) {
                // Check if form data exists for this rate plan
                if (!isset($formData[$ratePlanId]) || empty($formData[$ratePlanId])) {
                    \Log::warning('No form data for rate plan', ['ratePlanId' => $ratePlanId]);
                    continue;
                }

                // Process this rate plan's pricing
                $planResult = $this->saveRatePlanPricing($ratePlanId, $ratePlan, $formData[$ratePlanId], $dateRange);
                $results['updatedPeriods'] += $planResult['periods'];
                $results['updatedExceptions'] += $planResult['exceptions'];

                if (!empty($planResult['errors'])) {
                    $results['errors'] = array_merge($results['errors'], $planResult['errors']);
                }
            }

            if (!empty($results['errors'])) {
                $results['success'] = false;
                $results['message'] = 'Bazı fiyatlar kaydedilirken hatalar oluştu.';
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving pricing data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $results['success'] = false;
            $results['message'] = 'Fiyatlar kaydedilirken bir hata oluştu: ' . $e->getMessage();
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Save pricing data for a specific rate plan
     *
     * @param int $ratePlanId Rate plan ID
     * @param array $ratePlan Rate plan data
     * @param array $formData Form data for this rate plan
     * @param array $dateRange Date range to process
     * @return array Results with stats and errors
     */
    public function saveRatePlanPricing(int $ratePlanId, array $ratePlan, array $formData, array $dateRange): array
    {
        $result = [
            'periods' => 0,
            'exceptions' => 0,
            'errors' => [],
        ];

        try {
            // Log the optimization process
            \Log::info('Starting rate plan pricing save', [
                'ratePlanId' => $ratePlanId,
                'dateCount' => count($dateRange)
            ]);

            // Group dates by pricing data to create optimized periods
            $groupedDates = $this->groupDatesByPricing($ratePlanId, $ratePlan, $formData, $dateRange);

            \Log::info('Pricing data optimized', [
                'ratePlanId' => $ratePlanId,
                'groupCount' => count($groupedDates),
                'dateCount' => count($dateRange)
            ]);

            foreach ($groupedDates as $group) {
                $startDate = Carbon::parse($group['dates'][0]);
                $endDate = Carbon::parse($group['dates'][count($group['dates']) - 1]);
                $pricingData = $group['pricing'];

                // Log period being created/updated
                \Log::info('Processing pricing period', [
                    'startDate' => $startDate->format('Y-m-d'),
                    'endDate' => $endDate->format('Y-m-d'),
                    'basePrice' => $pricingData['base_price'] ?? 0,
                    'daysCount' => count($group['dates']),
                    'exceptionCount' => count($group['exceptions'] ?? [])
                ]);

                // Ensure that base_price is not null if prices is null
                // For per-person pricing, we need either base_price or prices
                $isPerPerson = $ratePlan['is_per_person'] ?? false;

                // First, ensure base_price is never null by setting a default value
                if (!isset($pricingData['base_price']) || $pricingData['base_price'] === null) {
                    $pricingData['base_price'] = 0;
                }

                if ($isPerPerson) {
                    // For per-person pricing, make sure prices array is not empty
                    if (empty($pricingData['prices']) || !is_array($pricingData['prices'])) {
                        // If no per-person prices, use base_price with default
                        if ($pricingData['base_price'] === null) {
                            $pricingData['base_price'] = 0;
                        }
                    }
                } else {
                    // For non per-person pricing, make sure base_price is not null
                    if (empty($pricingData['base_price']) && $pricingData['base_price'] !== 0) {
                        $pricingData['base_price'] = 0;
                    }

                    // Ensure prices is null for non per-person pricing
                    $pricingData['prices'] = null;
                }

                // Find existing periods that overlap with this date range
                $overlappingPeriods = $this->findOverlappingPeriods(
                    $ratePlanId,
                    $startDate,
                    $endDate
                );

                if ($overlappingPeriods->isEmpty()) {
                    // No overlapping periods, create a new one
                    $this->createOrUpdateRatePeriod($ratePlanId, array_merge($pricingData, [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]));
                    $result['periods']++;
                } else {
                    // Check if we can update an existing period that exactly matches our date range
                    $exactPeriod = $overlappingPeriods->first(function ($period) use ($startDate, $endDate) {
                        return $period->start_date->isSameDay($startDate) && $period->end_date->isSameDay($endDate);
                    });

                    if ($exactPeriod) {
                        // Update the existing period
                        $this->createOrUpdateRatePeriod($ratePlanId, array_merge($pricingData, [
                            'id' => $exactPeriod->id,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ]));
                        $result['periods']++;
                    } else {
                        // If we have a complex overlap, create a new period
                        // The createOrUpdateRatePeriod method will handle splitting/merging as needed
                        $this->createOrUpdateRatePeriod($ratePlanId, array_merge($pricingData, [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ]));
                        $result['periods']++;
                    }
                }

                // Handle exceptions
                foreach ($group['exceptions'] as $date => $exceptionData) {
                    $this->handleExceptionSave($ratePlanId, $date, $exceptionData);
                    $result['exceptions']++;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error saving rate plan pricing: ' . $e->getMessage(), [
                'ratePlanId' => $ratePlanId,
                'trace' => $e->getTraceAsString()
            ]);

            $result['errors'][] = "Rate Plan ID {$ratePlanId}: " . $e->getMessage();
        }

        return $result;
    }

    /**
     * Groups dates by similar pricing data to create optimized periods
     * Also identifies exceptions automatically
     *
     * @param int $ratePlanId Rate plan ID
     * @param array $ratePlan Rate plan data
     * @param array $formData Form data for this rate plan
     * @param array $dateRange Date range to process
     * @return array Grouped dates with pricing data and exceptions
     */
    protected function groupDatesByPricing(int $ratePlanId, array $ratePlan, array $formData, array $dateRange): array
    {
        // First ensure ALL dates from dateRange are present in formData
        // This fixes issues with some dates being skipped
        $isPerPerson = $ratePlan['is_per_person'] ?? false;
        
        // Calculate a default pricing template for any missing dates
        $defaultPricing = [
            'base_price' => 0,
            'prices' => $isPerPerson ? [] : null,
            'min_stay' => 1,
            'quantity' => 1,
            'sales_type' => 'direct',
            'status' => true,
            'is_exception' => false,
        ];
        
        // Log the date range and form data to assist with debugging
        \Log::info('Starting date grouping with range', [
            'ratePlanId' => $ratePlanId,
            'dateRangeCount' => count($dateRange),
            'formDataCount' => count($formData),
            'firstDate' => $dateRange[0] ?? 'none',
            'lastDate' => $dateRange[count($dateRange) - 1] ?? 'none',
        ]);
        
        // Make a copy of formData to ensure we don't modify it directly
        $completeFormData = $formData;
        
        // Ensure all dates exist in form data
        foreach ($dateRange as $date) {
            if (!isset($completeFormData[$date])) {
                // If a date is missing from form data, add it with default values
                \Log::warning('Date missing from form data - adding defaults', [
                    'date' => $date,
                    'ratePlanId' => $ratePlanId
                ]);
                $completeFormData[$date] = $defaultPricing;
            }
        }
        
        // Replace the original formData with our complete version for further processing
        $formData = $completeFormData;
        
        // Start the analysis after ensuring all dates exist
        $isUniform = true;
        $firstDate = null;
        $firstPricing = null;
        $exceptionDates = [];

        // Find first non-exception date to use as reference
        foreach ($dateRange as $date) {
            if (isset($formData[$date]) && !($formData[$date]['is_exception'] ?? false)) {
                $firstDate = $date;
                break;
            }
        }

        // If no non-exception date found, just use the first date
        if ($firstDate === null && !empty($dateRange)) {
            $firstDate = $dateRange[0];
        }

        // Return empty if still no data available
        if ($firstDate === null) {
            return [];
        }

        // Get the first date's pricing as a reference
        $firstFormData = $formData[$firstDate];
        $firstPricing = [
            'base_price' => isset($firstFormData['base_price']) && $firstFormData['base_price'] !== null
                ? (float)$firstFormData['base_price']
                : 0,
            'prices' => $isPerPerson ? ($firstFormData['prices'] ?? []) : null,
            'min_stay' => isset($firstFormData['min_stay']) && $firstFormData['min_stay']
                ? (int)$firstFormData['min_stay']
                : 1,
            'quantity' => isset($firstFormData['quantity']) && $firstFormData['quantity']
                ? (int)$firstFormData['quantity']
                : 1,
            'sales_type' => (string)($firstFormData['sales_type'] ?? 'direct'),
            'status' => $firstFormData['status'] ?? true,
        ];

        // Check if all dates (except exceptions) have the same pricing
        foreach ($dateRange as $date) {
            if (!isset($formData[$date])) {
                continue;
            }

            $dateFormData = $formData[$date];

            // If it's marked as an exception, track it separately
            if ($dateFormData['is_exception'] ?? false) {
                $exceptionDates[$date] = true;
                continue;
            }

            // Extract pricing for this date
            $pricing = [
                'base_price' => isset($dateFormData['base_price']) && $dateFormData['base_price'] !== null
                    ? (float)$dateFormData['base_price']
                    : 0,
                'prices' => $isPerPerson ? ($dateFormData['prices'] ?? []) : null,
                'min_stay' => isset($dateFormData['min_stay']) && $dateFormData['min_stay']
                    ? (int)$dateFormData['min_stay']
                    : 1,
                'quantity' => isset($dateFormData['quantity']) && $dateFormData['quantity']
                    ? (int)$dateFormData['quantity']
                    : 1,
                'sales_type' => (string)($dateFormData['sales_type'] ?? 'direct'),
                'status' => $dateFormData['status'] ?? true,
            ];

            // Compare with first pricing
            if (!$this->isPricingEqual($firstPricing, $pricing)) {
                $isUniform = false;
                break;
            }
        }

        // If all pricing is uniform, create a single period with exceptions
        if ($isUniform && count($dateRange) > 1) {
            // Create the main group for all dates
            $mainGroup = [
                'pricing' => $firstPricing,
                'dates' => [],
                'exceptions' => [],
            ];

            // Add all non-exception dates to the main group
            foreach ($dateRange as $date) {
                if (!isset($formData[$date])) {
                    continue;
                }

                if (!isset($exceptionDates[$date])) {
                    $mainGroup['dates'][] = $date;
                } else {
                    // Add exceptions
                    $exceptionData = $formData[$date];
                    $mainGroup['exceptions'][$date] = [
                        'base_price' => isset($exceptionData['base_price']) && $exceptionData['base_price'] !== null
                            ? (float)$exceptionData['base_price']
                            : 0,
                        'prices' => $isPerPerson ? ($exceptionData['prices'] ?? []) : null,
                        'min_stay' => isset($exceptionData['min_stay']) && $exceptionData['min_stay']
                            ? (int)$exceptionData['min_stay']
                            : 1,
                        'quantity' => isset($exceptionData['quantity']) && $exceptionData['quantity']
                            ? (int)$exceptionData['quantity']
                            : 1,
                        'sales_type' => (string)($exceptionData['sales_type'] ?? 'direct'),
                        'status' => $exceptionData['status'] ?? true,
                    ];
                }
            }

            \Log::info('Optimized pricing: Created single period for all dates', [
                'ratePlanId' => $ratePlanId,
                'dateCount' => count($mainGroup['dates']),
                'exceptionCount' => count($mainGroup['exceptions']),
                'basePrice' => $firstPricing['base_price']
            ]);

            return [$mainGroup];
        }

        // If we're here, pricing is not uniform or we have only one date - group by pricing similarity
        $groupedDates = [];
        $currentGroup = null;

        foreach ($dateRange as $date) {
            if (!isset($formData[$date])) {
                continue;
            }

            $dateFormData = $formData[$date];

            // Check if this date is marked as an exception
            $isException = $dateFormData['is_exception'] ?? false;

            // Extract pricing data (excluding is_exception and exception_id)
            $pricing = [
                // Always ensure base_price is never null regardless of pricing type
                'base_price' => isset($dateFormData['base_price']) && $dateFormData['base_price'] !== null
                    ? (float)$dateFormData['base_price']
                    : 0,
                'prices' => $isPerPerson ? ($dateFormData['prices'] ?? []) : null,
                'min_stay' => isset($dateFormData['min_stay']) && $dateFormData['min_stay']
                    ? (int)$dateFormData['min_stay']
                    : 1,
                'quantity' => isset($dateFormData['quantity']) && $dateFormData['quantity']
                    ? (int)$dateFormData['quantity']
                    : 1,
                'sales_type' => (string)($dateFormData['sales_type'] ?? 'direct'),
                'status' => $dateFormData['status'] ?? true,
            ];

            if ($isException) {
                // Add exception to current group
                if ($currentGroup) {
                    $currentGroup['exceptions'][$date] = $pricing;
                }
                continue;
            }

            // Check if we need to start a new group
            if ($currentGroup === null) {
                $currentGroup = [
                    'pricing' => $pricing,
                    'dates' => [$date],
                    'exceptions' => [],
                ];
                $groupedDates[] = &$currentGroup;
            } elseif ($this->isPricingEqual($currentGroup['pricing'], $pricing)) {
                // Add to existing group
                $currentGroup['dates'][] = $date;
            } else {
                // Start a new group
                $currentGroup = [
                    'pricing' => $pricing,
                    'dates' => [$date],
                    'exceptions' => [],
                ];
                $groupedDates[] = &$currentGroup;
            }
        }

        return $groupedDates;
    }

    /**
     * Compare if two pricing data arrays are equal
     *
     * @param array $pricing1 First pricing data
     * @param array $pricing2 Second pricing data
     * @return bool True if pricing is equal
     */
    protected function isPricingEqual(array $pricing1, array $pricing2): bool
    {
        // Compare base price, min_stay, quantity, sales_type, and status
        if ($pricing1['base_price'] != $pricing2['base_price'] ||
            $pricing1['min_stay'] != $pricing2['min_stay'] ||
            $pricing1['quantity'] != $pricing2['quantity'] ||
            $pricing1['sales_type'] != $pricing2['sales_type'] ||
            $pricing1['status'] != $pricing2['status']) {
            return false;
        }

        // Compare prices arrays
        if (is_array($pricing1['prices']) && is_array($pricing2['prices'])) {
            // Check if they have the same keys
            if (array_diff_key($pricing1['prices'], $pricing2['prices']) ||
                array_diff_key($pricing2['prices'], $pricing1['prices'])) {
                return false;
            }

            // Check if values are equal
            foreach ($pricing1['prices'] as $key => $value) {
                if (!isset($pricing2['prices'][$key]) || $pricing2['prices'][$key] != $value) {
                    return false;
                }
            }
        } elseif ($pricing1['prices'] !== $pricing2['prices']) {
            return false;
        }

        return true;
    }

    /**
     * Handle saving an exception for a specific date
     *
     * @param int $ratePlanId Rate plan ID
     * @param string $date Date string (Y-m-d)
     * @param array $exceptionData Exception data
     * @return RateException|null Created or updated exception, or null on failure
     */
    protected function handleExceptionSave(int $ratePlanId, string $date, array $exceptionData): ?RateException
    {
        try {
            // Find the period that contains this date
            $period = $this->ratePeriodRepository->findPeriodContainingDate($ratePlanId, $date);

            if (!$period) {
                Log::warning("No period found for exception date", [
                    'ratePlanId' => $ratePlanId,
                    'date' => $date
                ]);
                return null;
            }

            // Create or update exception using repository
            return $this->rateExceptionRepository->createOrUpdate($period->id, $date, $exceptionData);

        } catch (\Exception $e) {
            Log::error('Error handling exception save: ' . $e->getMessage(), [
                'ratePlanId' => $ratePlanId,
                'date' => $date,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Process form data to identify and handle exceptions automatically
     *
     * @param int $ratePlanId Rate plan ID
     * @param array $ratePlan Rate plan data
     * @param array $formData Form data for this date
     * @param string $date Date string (Y-m-d)
     * @return array Updated form data with exception information
     */
    public function processExceptionFromFormData(int $ratePlanId, array $ratePlan, array $formData, string $date): array
    {
        $originalData = $formData['original_data'] ?? [];
        $hasChanged = false;
        $isPerPerson = $ratePlan['is_per_person'] ?? false;

        // Check if any field has changed
        if ((isset($formData['base_price']) && $formData['base_price'] != ($originalData['base_price'] ?? null)) ||
            (!empty($formData['prices']) && json_encode($formData['prices']) != json_encode($originalData['prices'] ?? [])) ||
            $formData['min_stay'] != ($originalData['min_stay'] ?? 1) ||
            $formData['quantity'] != ($originalData['quantity'] ?? 1) ||
            $formData['sales_type'] != ($originalData['sales_type'] ?? 'direct') ||
            $formData['status'] != ($originalData['status'] ?? true)) {
            $hasChanged = true;
        }

        // If nothing changed, return original data
        if (!$hasChanged) {
            return $formData;
        }

        try {
            // Get the period containing this date
            $period = $this->ratePeriodRepository->findPeriodContainingDate($ratePlanId, $date);

            if (!$period) {
                Log::warning('No period found for date when trying to handle exception', [
                    'date' => $date,
                    'ratePlanId' => $ratePlanId
                ]);
                return $formData;
            }

            // Set base_price to 0 if null, regardless of pricing type
            $basePrice = $formData['base_price'];
            if ($basePrice === null) {
                $basePrice = 0;
            }

            $exceptionData = [
                'base_price' => $basePrice,
                'prices' => $isPerPerson ? ($formData['prices'] ?? []) : null,
                'min_stay' => $formData['min_stay'] ?? 1,
                'quantity' => $formData['quantity'] ?? 1,
                'sales_type' => (string)($formData['sales_type'] ?? 'direct'),
                'status' => $formData['status'] ?? true,
            ];

            // Double-check to make sure base_price is never null
            if (!isset($exceptionData['base_price']) || $exceptionData['base_price'] === null) {
                $exceptionData['base_price'] = 0;
            }

            // For per-person pricing with empty prices array, ensure base_price is set
            if ($isPerPerson && (empty($exceptionData['prices']) || !is_array($exceptionData['prices']))) {
                if ($exceptionData['base_price'] === null) {
                    $exceptionData['base_price'] = 0;
                }
            }

            // Check if this is a new exception or updating an existing one
            if ($formData['is_exception'] ?? false) {
                $exceptionId = $formData['exception_id'] ?? null;
                if ($exceptionId) {
                    // Update existing exception
                    if ($hasChanged) {
                        $exception = $this->rateExceptionRepository->find($exceptionId);
                        if ($exception) {
                            $this->rateExceptionRepository->update($exceptionId, $exceptionData);

                            Log::info('Updated existing exception', [
                                'date' => $date,
                                'ratePlanId' => $ratePlanId,
                                'exceptionId' => $exceptionId,
                                'basePrice' => $exceptionData['base_price'],
                                'pricesArray' => $exceptionData['prices']
                            ]);
                        } else {
                            // Exception ID provided but not found, create a new one
                            $exception = $this->rateExceptionRepository->createOrUpdate($period->id, $date, $exceptionData);
                            if ($exception) {
                                $formData['exception_id'] = $exception->id;

                                Log::info('Created replacement exception after missing original', [
                                    'date' => $date,
                                    'ratePlanId' => $ratePlanId,
                                    'oldExceptionId' => $exceptionId,
                                    'newExceptionId' => $exception->id
                                ]);
                            }
                        }
                    }
                }
            } else if ($hasChanged) {
                // Check if exception exists for this date
                $exception = $this->rateExceptionRepository->findByDate($period->id, $date);

                if ($exception) {
                    // Update the existing exception
                    $this->rateExceptionRepository->update($exception->id, $exceptionData);

                    // Mark as exception in form data
                    $formData['is_exception'] = true;
                    $formData['exception_id'] = $exception->id;

                    Log::info('Updated existing exception during detection', [
                        'date' => $date,
                        'ratePlanId' => $ratePlanId,
                        'exceptionId' => $exception->id
                    ]);
                } else {
                    // Create a new exception
                    $exception = $this->rateExceptionRepository->create(array_merge($exceptionData, [
                        'rate_period_id' => $period->id,
                        'date' => $date,
                    ]));

                    // Mark as exception in form data
                    $formData['is_exception'] = true;
                    $formData['exception_id'] = $exception->id;

                    Log::info('Created new exception', [
                        'date' => $date,
                        'ratePlanId' => $ratePlanId,
                        'exceptionId' => $exception->id
                    ]);
                }
            }

            // Update form data with the latest values from the exception
            if ($formData['is_exception'] && isset($formData['exception_id'])) {
                $exception = $this->rateExceptionRepository->find($formData['exception_id']);
                if ($exception) {
                    // Update form data with actual values from the database
                    $formData['base_price'] = $exception->base_price;
                    $formData['prices'] = $exception->prices;
                    $formData['min_stay'] = $exception->min_stay;
                    $formData['quantity'] = $exception->quantity;
                    $formData['sales_type'] = $exception->sales_type;
                    $formData['status'] = $exception->status;
                }
            }
        } catch (\Exception $e) {
            // Log error but continue
            Log::error('Exception processing failed: ' . $e->getMessage(), [
                'date' => $date,
                'ratePlanId' => $ratePlanId,
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $formData;
    }
}