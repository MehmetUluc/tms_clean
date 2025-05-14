<?php

namespace App\Plugins\Pricing\Services;

use App\Plugins\Pricing\Models\DailyRate;
use App\Plugins\Pricing\Models\RatePlan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyRateService
{
    /**
     * Cache key for rate plan prices
     */
    private function getRatePlanCacheKey(int $ratePlanId, string $date): string
    {
        return "rate_plan_{$ratePlanId}_price_{$date}";
    }

    /**
     * Get daily rate for a specific date
     */
    public function getDailyRate(int $ratePlanId, string $date): ?DailyRate
    {
        $cacheKey = $this->getRatePlanCacheKey($ratePlanId, $date);

        return Cache::remember($cacheKey, 3600, function () use ($ratePlanId, $date) {
            return DailyRate::where('rate_plan_id', $ratePlanId)
                ->where('date', $date)
                ->first();
        });
    }

    /**
     * Get daily rates for a date range
     */
    public function getDailyRates(int $ratePlanId, string $startDate, string $endDate): Collection
    {
        $rates = DailyRate::where('rate_plan_id', $ratePlanId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Process each rate to ensure prices_json is properly decoded if needed
        foreach ($rates as $rate) {
            if ($rate->is_per_person && $rate->prices_json) {
                // If prices_json is a string, decode it
                if (is_string($rate->prices_json)) {
                    try {
                        // Decode JSON string to array for easier use in the form
                        $decodedPrices = json_decode($rate->prices_json, true);
                        if (is_array($decodedPrices)) {
                            // Store the decoded prices in the 'prices' attribute for the form to use
                            $rate->setAttribute('prices', $decodedPrices);

                            // Prices decoded successfully
                        }
                    } catch (\Exception $e) {
                        Log::warning('Failed to decode prices_json: ' . $e->getMessage());
                    }
                }
            }
        }

        return $rates;
    }

    /**
     * Save a single daily rate
     */
    public function saveDailyRate(int $ratePlanId, string $date, array $data): DailyRate
    {
        $rateData = array_merge(['rate_plan_id' => $ratePlanId, 'date' => $date], $data);
        
        $dailyRate = DailyRate::updateOrCreate(
            ['rate_plan_id' => $ratePlanId, 'date' => $date],
            $rateData
        );

        // Clear cache for this date
        $cacheKey = $this->getRatePlanCacheKey($ratePlanId, $date);
        Cache::forget($cacheKey);

        return $dailyRate;
    }

    /**
     * Bulk save daily rates for a date range
     * This uses efficient bulk insertion to maximize performance
     */
    public function bulkSaveDailyRates(int $ratePlanId, string $startDate, string $endDate, array $data): bool
    {
        $startDateObj = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);
        
        // Get a list of all dates in the range
        $dates = [];
        $period = CarbonPeriod::create($startDateObj, $endDateObj);
        
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Get existing rates in this date range
            $existingRates = DailyRate::where('rate_plan_id', $ratePlanId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->keyBy(function ($rate) {
                    return $rate->date->format('Y-m-d');
                });
            
            // Prepare batch insert data
            $batchInsertData = [];
            $now = now();
            
            foreach ($dates as $date) {
                // If rate exists, update it
                if ($existingRates->has($date)) {
                    $existingRate = $existingRates->get($date);
                    $existingRate->fill($data);
                    $existingRate->save();
                } else {
                    // Otherwise, prepare for batch insert
                    $batchInsertData[] = array_merge([
                        'rate_plan_id' => $ratePlanId,
                        'date' => $date,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], $data);
                }
            }
            
            // If we have data to insert, do a batch insert
            if (!empty($batchInsertData)) {
                // Chunk the inserts to prevent too large queries
                try {
                    // Chunk the inserts to prevent too large queries
                    foreach (array_chunk($batchInsertData, 500) as $chunk) {
                        try {
                            DailyRate::insert($chunk);
                        } catch (\Exception $chunkException) {
                            // Log detailed error but continue with other chunks
                            Log::error('Error inserting chunk: ' . $chunkException->getMessage(), [
                                'rate_plan_id' => $ratePlanId,
                                'chunk_size' => count($chunk),
                                'exception' => $chunkException,
                                'first_date' => $chunk[0]['date'] ?? 'unknown',
                                'last_date' => $chunk[count($chunk)-1]['date'] ?? 'unknown',
                            ]);
                            
                            // Try inserting records one by one
                            foreach ($chunk as $record) {
                                try {
                                    DailyRate::create($record);
                                } catch (\Exception $recordException) {
                                    Log::error('Failed to insert individual record: ' . $recordException->getMessage(), [
                                        'rate_plan_id' => $ratePlanId,
                                        'date' => $record['date'] ?? 'unknown',
                                        'exception' => $recordException
                                    ]);
                                }
                            }
                        }
                    }
                } catch (\Exception $insertException) {
                    // Log but don't throw to allow transaction to complete if possible
                    Log::error('Error in batch insert process: ' . $insertException->getMessage(), [
                        'rate_plan_id' => $ratePlanId,
                        'exception' => $insertException
                    ]);
                }
            }
            
            // Commit transaction
            DB::commit();
            
            // Clear cache for this date range
            foreach ($dates as $date) {
                $cacheKey = $this->getRatePlanCacheKey($ratePlanId, $date);
                Cache::forget($cacheKey);
            }
            
            return true;
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();
            Log::error('Failed to bulk save daily rates: ' . $e->getMessage(), [
                'rate_plan_id' => $ratePlanId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'exception' => $e
            ]);
            
            return false;
        }
    }

    /**
     * Save daily rates from OTA format (array of dates with prices)
     * 
     * Expected format:
     * [
     *     '2025-05-15' => [
     *         'base_price' => 100.00,
     *         'is_closed' => false,
     *         // other rate data
     *     ],
     *     '2025-05-16' => [
     *         'base_price' => 110.00,
     *         'is_closed' => false,
     *         // other rate data
     *     ],
     *     // more dates
     * ]
     */
    public function saveDailyRatesFromArray(int $ratePlanId, array $ratesData): bool
    {
        if (empty($ratesData)) {
            Log::debug('DailyRateService: Empty rates data provided', [
                'rate_plan_id' => $ratePlanId,
            ]);
            return false;
        }

        // Starting rate save

        // Check if rate plan exists
        try {
            $ratePlan = RatePlan::findOrFail($ratePlanId);
            // Rate plan found
        } catch (\Exception $e) {
            Log::error('DailyRateService: Rate plan not found', [
                'rate_plan_id' => $ratePlanId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get all dates from the rates data
            $dates = array_keys($ratesData);

            if (empty($dates)) {
                Log::warning('DailyRateService: No dates in rates data', ['rate_plan_id' => $ratePlanId]);
                return false;
            }

            $minDate = min($dates);
            $maxDate = max($dates);

            // Date range determined

            // Get existing rates in this date range
            $existingRates = DailyRate::where('rate_plan_id', $ratePlanId)
                ->whereBetween('date', [$minDate, $maxDate])
                ->get()
                ->keyBy(function ($rate) {
                    return $rate->date->format('Y-m-d');
                });

            // Found existing rates

            // Prepare batch insert data
            $batchInsertData = [];
            $now = now();
            
            foreach ($ratesData as $date => $rateData) {
                // Processing date data

                // Get information about the rate plan's room for pricing method
                try {
                    $room = $ratePlan->room;
                    $isPerPerson = $room->pricing_calculation_method === 'per_person';

                    // Prepare data for database saving with correct field names
                    // Check if we have per-person pricing data in the input
                    if ($isPerPerson && isset($rateData['prices']) && is_array($rateData['prices'])) {
                        // For is_per_person=true, we need to save the prices array as JSON
                        $rateData['prices_json'] = json_encode($rateData['prices']);
                        $rateData['is_per_person'] = true;

                        // Using per-person pricing data
                    }
                    // Check if we already have prices_json in the input data (direct format)
                    else if (isset($rateData['prices_json']) && $rateData['prices_json'] !== null) {
                        // Make sure prices_json is a string (JSON)
                        if (!is_string($rateData['prices_json'])) {
                            // If it's already an array, encode it as JSON
                            if (is_array($rateData['prices_json'])) {
                                $rateData['prices_json'] = json_encode($rateData['prices_json']);
                            }
                            // If it's already a decoded JSON object, encode it
                            else if (is_object($rateData['prices_json'])) {
                                $rateData['prices_json'] = json_encode($rateData['prices_json']);
                            }
                        }

                        // Using provided prices_json directly

                        // Ensure is_per_person flag is set
                        $rateData['is_per_person'] = $isPerPerson;
                    }
                    else {
                        $rateData['is_per_person'] = $isPerPerson;
                        $rateData['prices_json'] = null; // For unit pricing, no prices JSON needed

                        // No per-person pricing data, using null prices_json
                    }

                    // Add refund information if available
                    if (isset($rateData['refund_options'])) {
                        $rateData['is_refundable'] = $rateData['refund_options']['is_refundable'] ?? true;

                        // Added refund information
                    }
                } catch (\Exception $e) {
                    Log::warning('DailyRateService: Could not get room pricing information', [
                        'rate_plan_id' => $ratePlanId,
                        'date' => $date,
                        'error' => $e->getMessage()
                    ]);
                }

                // Check for any null values in rate data and set defaults
                foreach ($rateData as $key => $value) {
                    if ($value === null) {
                        switch ($key) {
                            case 'base_price':
                                $rateData[$key] = 0;
                                break;
                            case 'min_stay_arrival':
                                $rateData[$key] = 1;
                                break;
                            case 'is_closed':
                                $rateData[$key] = false;
                                break;
                            case 'status':
                                $rateData[$key] = 'available';
                                break;
                            default:
                                // Leave other nulls as is
                                break;
                        }
                    }

                    // Additional type checking and converting for base_price
                    if ($key === 'base_price') {
                        if (is_string($value) && trim($value) === '') {
                            $rateData[$key] = 0;
                        } elseif (!is_null($value)) {
                            // Force to numeric
                            $rateData[$key] = is_numeric($value) ? floatval($value) : 0;
                        }
                    }
                }

                // Ensure required fields have values
                if (!isset($rateData['base_price'])) {
                    $rateData['base_price'] = 0;
                }

                if (!isset($rateData['currency'])) {
                    $rateData['currency'] = 'TRY';
                }

                if (!isset($rateData['min_stay_arrival'])) {
                    $rateData['min_stay_arrival'] = 1;
                }

                if (!isset($rateData['status'])) {
                    $rateData['status'] = 'available';
                }

                // Set default sales_type to direct if not specified
                if (!isset($rateData['sales_type'])) {
                    $rateData['sales_type'] = 'direct';
                } elseif ($rateData['sales_type'] !== 'direct' && $rateData['sales_type'] !== 'ask_sell') {
                    // Ensure sales_type is one of the allowed values
                    $rateData['sales_type'] = 'direct';
                }

                // Processed date data

                // If rate exists, update it
                if ($existingRates->has($date)) {
                    try {
                        $existingRate = $existingRates->get($date);
                        $existingRate->fill($rateData);
                        $existingRate->save();

                        // Updated existing rate
                    } catch (\Exception $e) {
                        Log::error('DailyRateService: Failed to update existing rate', [
                            'rate_plan_id' => $ratePlanId,
                            'date' => $date,
                            'error' => $e->getMessage(),
                            'data' => $rateData,
                        ]);
                    }
                } else {
                    // Otherwise, prepare for batch insert
                    $batchInsertData[] = array_merge([
                        'rate_plan_id' => $ratePlanId,
                        'date' => $date,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], $rateData);

                    // Prepared new rate for insertion
                }
            }
            
            // If we have data to insert, do a batch insert
            if (!empty($batchInsertData)) {
                // Starting batch insert

                try {
                    $successCount = 0;
                    $errorCount = 0;

                    // Processing batch data

                    // First, try to save records one by one for better error handling
                    foreach ($batchInsertData as $index => $record) {
                        try {
                            // Fix for prices_json field - ensure it's passed correctly
                            if (isset($record['prices_json']) && $record['prices_json'] !== null && is_string($record['prices_json'])) {
                                // Prices JSON exists and is properly formatted
                            } else if (isset($record['is_per_person']) && $record['is_per_person'] === true && (!isset($record['prices_json']) || $record['prices_json'] === null)) {
                                Log::warning('Per-person pricing without prices_json for date: ' . ($record['date'] ?? 'unknown'));
                            }

                            // Inserting record

                            // About to insert rate record

                            $newRate = DailyRate::create($record);
                            $successCount++;

                            // Successfully inserted rate
                        } catch (\Exception $recordException) {
                            $errorCount++;
                            Log::error('DailyRateService: Failed to insert individual record: ' . $recordException->getMessage(), [
                                'rate_plan_id' => $ratePlanId,
                                'date' => $record['date'] ?? 'unknown',
                                'data' => $record,
                                'base_price' => $record['base_price'] ?? 'missing',
                                'base_price_type' => gettype($record['base_price'] ?? null),
                                'sql_error' => $recordException->getMessage(),
                                'trace' => $recordException->getTraceAsString(),
                            ]);

                            // Try one more time with fallback values
                            try {
                                $fallbackRecord = $record;
                                $fallbackRecord['base_price'] = 0;

                                // Trying fallback insertion with zero price

                                $newRate = DailyRate::create($fallbackRecord);
                                $successCount++;

                                // Fallback insertion succeeded
                            } catch (\Exception $fallbackException) {
                                Log::error('DailyRateService: Fallback insertion also failed: ' . $fallbackException->getMessage(), [
                                    'rate_plan_id' => $ratePlanId,
                                    'date' => $fallbackRecord['date'] ?? 'unknown',
                                ]);
                            }
                        }
                    }

                    // Batch insert completed
                } catch (\Exception $insertException) {
                    // Log but don't throw to allow transaction to complete if possible
                    Log::error('DailyRateService: Error in batch insertion process: ' . $insertException->getMessage(), [
                        'rate_plan_id' => $ratePlanId,
                        'batch_size' => count($batchInsertData),
                        'exception' => $insertException,
                        'trace' => $insertException->getTraceAsString(),
                    ]);
                }
            }
            
            // Commit transaction
            DB::commit();

            // Clear cache for this date range
            foreach ($dates as $date) {
                $cacheKey = $this->getRatePlanCacheKey($ratePlanId, $date);
                Cache::forget($cacheKey);
            }

            Log::info('Successfully saved rates for rate plan ' . $ratePlanId . ', ' . count($dates) . ' dates processed');

            return true;
        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();
            Log::error('DailyRateService: Failed to save daily rates from array: ' . $e->getMessage(), [
                'rate_plan_id' => $ratePlanId,
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Delete daily rates for a specific date range
     */
    public function deleteDailyRates(int $ratePlanId, string $startDate, string $endDate): bool
    {
        try {
            // Get all dates in the range
            $startDateObj = Carbon::parse($startDate);
            $endDateObj = Carbon::parse($endDate);
            $dates = [];
            
            foreach (CarbonPeriod::create($startDateObj, $endDateObj) as $date) {
                $dates[] = $date->format('Y-m-d');
            }
            
            // Delete the rates
            DailyRate::where('rate_plan_id', $ratePlanId)
                ->whereBetween('date', [$startDate, $endDate])
                ->delete();
            
            // Clear cache for this date range
            foreach ($dates as $date) {
                $cacheKey = $this->getRatePlanCacheKey($ratePlanId, $date);
                Cache::forget($cacheKey);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete daily rates: ' . $e->getMessage(), [
                'rate_plan_id' => $ratePlanId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'exception' => $e
            ]);
            
            return false;
        }
    }

    /**
     * Get the best available rate for a room on a specific date
     */
    public function getBestAvailableRate(int $roomId, string $date): ?DailyRate
    {
        // Get all rate plans for this room
        $ratePlans = RatePlan::where('room_id', $roomId)
            ->where('is_active', true)
            ->get();
        
        if ($ratePlans->isEmpty()) {
            return null;
        }
        
        $bestRate = null;
        
        // Find the lowest price rate for this date
        foreach ($ratePlans as $ratePlan) {
            $rate = $this->getDailyRate($ratePlan->id, $date);
            
            if ($rate && !$rate->is_closed && $rate->status !== 'sold_out') {
                if (!$bestRate || $rate->base_price < $bestRate->base_price) {
                    $bestRate = $rate;
                }
            }
        }
        
        return $bestRate;
    }
}