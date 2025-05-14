<?php

namespace App\Plugins\Discount\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Discount\Contracts\DiscountServiceInterface;
use App\Plugins\Pricing\Services\DiscountedPriceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    protected $discountService;
    protected $discountedPriceService;

    /**
     * Constructor
     */
    public function __construct(
        DiscountServiceInterface $discountService,
        DiscountedPriceService $discountedPriceService
    ) {
        $this->discountService = $discountService;
        $this->discountedPriceService = $discountedPriceService;
    }

    /**
     * Validate a discount code
     */
    public function validateCode(Request $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'code' => 'required|string',
            'hotel_id' => 'nullable|integer',
            'room_id' => 'nullable|integer',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'occupancy' => 'nullable|integer|min:1',
        ]);

        // Validate the discount code
        $result = $this->discountedPriceService->validateDiscountCode(
            $validated['code'],
            $validated['hotel_id'] ?? null,
            $validated['room_id'] ?? null,
            $validated['check_in'] ?? null,
            $validated['check_out'] ?? null,
            $validated['occupancy'] ?? 1
        );

        // Return the result
        return response()->json($result);
    }

    /**
     * Calculate discounted prices for a reservation
     */
    public function calculatePrice(Request $request): JsonResponse
    {
        // Validate the request
        $validated = $request->validate([
            'rate_plan_id' => 'required|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'occupancy' => 'nullable|integer|min:1',
            'discount_code' => 'nullable|string',
        ]);

        // Calculate discounted price
        try {
            $result = $this->discountedPriceService->calculateDiscountedPricesForStay(
                $validated['rate_plan_id'],
                $validated['check_in'],
                $validated['check_out'],
                $validated['occupancy'] ?? 1,
                $validated['discount_code'] ?? null
            );

            // Format discount information for response
            $discountInfo = [
                'has_discount' => $result['has_discount'] ?? false,
                'discount_amount' => $result['discount_amount'] ?? 0,
                'discount_percentage' => $result['discount_percentage'] ?? 0,
                'original_total' => $result['original_total'] ?? $result['total_price'],
                'total_price' => $result['total_price'],
                'applied_discounts' => collect($result['applied_discounts'] ?? [])->map(function ($discount) {
                    return [
                        'id' => $discount->id,
                        'name' => $discount->name,
                        'description' => $discount->description,
                        'discount_type' => $discount->discount_type->value,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'available' => $result['available'] ?? false,
                'pricing' => $result,
                'discount' => $discountInfo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}