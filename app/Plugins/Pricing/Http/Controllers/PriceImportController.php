<?php

namespace App\Plugins\Pricing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Services\DailyRateService;
use App\Plugins\Pricing\Jobs\ProcessPriceImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PriceImportController extends Controller
{
    protected DailyRateService $dailyRateService;
    
    public function __construct(DailyRateService $dailyRateService)
    {
        $this->dailyRateService = $dailyRateService;
    }
    
    /**
     * Import prices from OTA or channel manager
     */
    public function import(Request $request)
    {
        // Validate basic request
        $validator = Validator::make($request->all(), [
            'rate_plan_id' => 'required|integer|exists:rate_plans,id',
            'prices' => 'required|array',
            'prices.*.date' => 'required|date',
            'prices.*.base_price' => 'required|numeric|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $ratePlanId = $request->input('rate_plan_id');
        $pricesData = $request->input('prices');
        
        try {
            // Check if rate plan exists
            $ratePlan = RatePlan::findOrFail($ratePlanId);
            
            // Format data for bulk save
            $formattedData = [];
            foreach ($pricesData as $priceItem) {
                $date = $priceItem['date'];
                unset($priceItem['date']); // Remove date from the array as it will be the key
                $formattedData[$date] = $priceItem;
            }
            
            // If we have a lot of dates, process in background
            if (count($formattedData) > 100) {
                ProcessPriceImport::dispatch($ratePlanId, $formattedData);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Price import job has been queued and will be processed shortly.',
                    'job_queued' => true
                ]);
            }
            
            // Otherwise process immediately
            $result = $this->dailyRateService->saveDailyRatesFromArray($ratePlanId, $formattedData);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prices imported successfully',
                    'count' => count($formattedData)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to import prices'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Price import failed: ' . $e->getMessage(), [
                'rate_plan_id' => $ratePlanId,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while importing prices: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk update prices for a date range
     */
    public function bulkUpdate(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'rate_plan_id' => 'required|integer|exists:rate_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'base_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'is_closed' => 'nullable|boolean',
            'min_stay_arrival' => 'nullable|integer|min:1',
            'status' => 'nullable|in:available,limited,sold_out',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $ratePlanId = $request->input('rate_plan_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Build data array from request, excluding certain fields
        $data = $request->except(['rate_plan_id', 'start_date', 'end_date']);
        
        try {
            // Check if rate plan exists
            $ratePlan = RatePlan::findOrFail($ratePlanId);
            
            // If date range is very large, process in background
            $dateDiff = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate));
            if ($dateDiff > 100) {
                ProcessPriceImport::dispatch($ratePlanId, $data, $startDate, $endDate, true);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Bulk update job has been queued and will be processed shortly.',
                    'job_queued' => true
                ]);
            }
            
            // Otherwise process immediately
            $result = $this->dailyRateService->bulkSaveDailyRates($ratePlanId, $startDate, $endDate, $data);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Prices updated successfully for date range',
                    'days_updated' => $dateDiff + 1 // +1 to include the end date
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update prices'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Bulk price update failed: ' . $e->getMessage(), [
                'rate_plan_id' => $ratePlanId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating prices: ' . $e->getMessage()
            ], 500);
        }
    }
}