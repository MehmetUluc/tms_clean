<?php

namespace App\Plugins\Pricing\Services;

use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Discount\Contracts\DiscountServiceInterface;
use App\Plugins\Pricing\Models\BookingPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Service for calculating discounted prices
 */
class DiscountedPriceService
{
    protected $pricingService;
    protected $discountService;

    /**
     * Constructor
     *
     * @param PricingService $pricingService
     * @param DiscountServiceInterface $discountService
     */
    public function __construct(
        PricingService $pricingService,
        DiscountServiceInterface $discountService
    ) {
        $this->pricingService = $pricingService;
        $this->discountService = $discountService;
    }

    /**
     * Calculate prices with applicable discounts for a stay
     *
     * @param int $ratePlanId
     * @param string|Carbon $checkIn
     * @param string|Carbon $checkOut
     * @param int $occupancy
     * @param string|null $discountCode
     * @return array
     */
    public function calculateDiscountedPricesForStay(
        int $ratePlanId,
        $checkIn,
        $checkOut,
        int $occupancy = 1,
        ?string $discountCode = null
    ): array {
        // First get standard pricing
        $priceData = $this->pricingService->calculatePricesForStay($ratePlanId, $checkIn, $checkOut, $occupancy);
        
        // If not available or no prices, return early
        if (!$priceData['available'] || empty($priceData['prices'])) {
            return $priceData;
        }
        
        // Get room information from rate plan
        $ratePlan = $priceData['rate_plan'];
        $roomId = $ratePlan->room_id;
        $hotelId = $ratePlan->hotel_id;
        
        // Prepare context for discount conditions
        $context = [
            'check_in_date' => $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn),
            'check_out_date' => $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut),
            'nights' => $checkIn instanceof Carbon ? $checkIn->diffInDays($checkOut) : Carbon::parse($checkIn)->diffInDays($checkOut),
            'occupancy' => $occupancy,
            'room_id' => $roomId,
            'hotel_id' => $hotelId,
            'booked_services' => [], // This would come from the reservation details
        ];
        
        // Find applicable discounts
        $hotelDiscounts = $this->discountService->findApplicableDiscounts('HOTEL', $hotelId, $context, $discountCode);
        $roomDiscounts = $this->discountService->findApplicableDiscounts('ROOM', $roomId, $context, $discountCode);
        
        // Combine all applicable discounts
        $allDiscounts = array_merge($hotelDiscounts, $roomDiscounts);
        
        // If no applicable discounts, return original pricing
        if (empty($allDiscounts)) {
            return array_merge($priceData, [
                'has_discount' => false,
                'discount_amount' => 0,
                'discount_percentage' => 0,
                'original_total' => $priceData['total_price'],
                'applied_discounts' => [],
            ]);
        }
        
        // Apply discounts to get discounted price
        [$discountedTotalPrice, $appliedDiscounts] = $this->discountService->applyStackableDiscounts(
            $allDiscounts,
            $priceData['total_price'],
            $context
        );
        
        // Calculate discount amounts
        $discountAmount = $priceData['total_price'] - $discountedTotalPrice;
        $discountPercentage = ($priceData['total_price'] > 0) 
            ? round(($discountAmount / $priceData['total_price']) * 100, 2)
            : 0;
        
        // Apply the same discount proportion to each day's price
        $discountedPrices = [];
        foreach ($priceData['prices'] as $date => $price) {
            $dayPrice = $price['price'];
            $dayDiscountedPrice = $dayPrice * ($discountedTotalPrice / $priceData['total_price']);
            
            $discountedPrices[$date] = array_merge($price, [
                'original_price' => $dayPrice,
                'discounted_price' => $dayDiscountedPrice,
                'discount_amount' => $dayPrice - $dayDiscountedPrice,
            ]);
        }
        
        // Return the results
        return array_merge($priceData, [
            'has_discount' => true,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'original_total' => $priceData['total_price'],
            'total_price' => $discountedTotalPrice,
            'prices' => $discountedPrices,
            'applied_discounts' => $appliedDiscounts,
        ]);
    }

    /**
     * Create price snapshots for a reservation with discounts
     *
     * @param Reservation $reservation
     * @param int $ratePlanId
     * @param int $occupancy
     * @param string|null $discountCode
     * @return array
     */
    public function createDiscountedReservationPriceSnapshots(
        Reservation $reservation,
        int $ratePlanId,
        int $occupancy = 1,
        ?string $discountCode = null
    ): array {
        // Calculate discounted prices
        $discountedPriceData = $this->calculateDiscountedPricesForStay(
            $ratePlanId,
            $reservation->check_in,
            $reservation->check_out,
            $occupancy,
            $discountCode
        );
        
        // If not available, throw exception
        if (!$discountedPriceData['available']) {
            throw new \Exception("Cannot create reservation: " . ($discountedPriceData['unavailable_reason'] ?? 'unavailable'));
        }
        
        // Use the pricing service to create snapshots
        $snapshotResult = $this->pricingService->createReservationPriceSnapshots(
            $reservation,
            $ratePlanId,
            $occupancy
        );
        
        // Record discount usage if discounts were applied
        if ($discountedPriceData['has_discount'] && !empty($discountedPriceData['applied_discounts'])) {
            foreach ($discountedPriceData['applied_discounts'] as $discount) {
                // Record usage for each applied discount
                $this->discountService->recordUsage(
                    $discount,
                    'reservation',
                    $reservation->id,
                    $discountedPriceData['discount_amount'],
                    $reservation->user_id,
                    $discountCode,
                    [
                        'total_price' => $discountedPriceData['original_total'],
                        'discounted_price' => $discountedPriceData['total_price'],
                        'discount_percentage' => $discountedPriceData['discount_percentage'],
                    ]
                );
            }
            
            // Update reservation with discount information
            $reservation->update([
                'original_price' => $discountedPriceData['original_total'],
                'discount_amount' => $discountedPriceData['discount_amount'],
                'discount_code' => $discountCode,
                'has_discount' => true,
            ]);
        }
        
        // Return the result
        return array_merge($snapshotResult, [
            'has_discount' => $discountedPriceData['has_discount'],
            'discount_amount' => $discountedPriceData['discount_amount'] ?? 0,
            'discount_percentage' => $discountedPriceData['discount_percentage'] ?? 0,
            'original_total' => $discountedPriceData['original_total'],
            'total_price' => $discountedPriceData['total_price'],
            'applied_discounts' => $discountedPriceData['applied_discounts'] ?? [],
        ]);
    }

    /**
     * Validate a discount code for a specific reservation scenario
     *
     * @param string $code
     * @param int|null $hotelId
     * @param int|null $roomId
     * @param string|Carbon|null $checkIn
     * @param string|Carbon|null $checkOut
     * @param int $occupancy
     * @return array
     */
    public function validateDiscountCode(
        string $code,
        ?int $hotelId = null,
        ?int $roomId = null,
        $checkIn = null,
        $checkOut = null,
        int $occupancy = 1
    ): array {
        // Basic validation of the code itself
        $discount = $this->discountService->validateDiscountCode($code);
        
        if (!$discount) {
            return [
                'valid' => false,
                'message' => 'Geçersiz indirim kodu.'
            ];
        }
        
        // If no reservation details provided, just return basic validity
        if (!$hotelId && !$roomId) {
            return [
                'valid' => true,
                'message' => 'Geçerli indirim kodu.',
                'discount' => $discount
            ];
        }
        
        // Prepare context for discount conditions
        $context = [
            'occupancy' => $occupancy,
        ];
        
        // Add date-related context if provided
        if ($checkIn && $checkOut) {
            $checkInDate = $checkIn instanceof Carbon ? $checkIn : Carbon::parse($checkIn);
            $checkOutDate = $checkOut instanceof Carbon ? $checkOut : Carbon::parse($checkOut);
            
            $context['check_in_date'] = $checkInDate;
            $context['check_out_date'] = $checkOutDate;
            $context['nights'] = $checkInDate->diffInDays($checkOutDate);
        }
        
        // Check if the discount applies to the selected hotel or room
        $applicable = false;
        
        // Check hotel-level applicability
        if ($hotelId) {
            $hotelDiscounts = $this->discountService->findApplicableDiscounts('HOTEL', $hotelId, $context, $code);
            $applicable = $applicable || !empty($hotelDiscounts);
        }
        
        // Check room-level applicability
        if ($roomId) {
            $roomDiscounts = $this->discountService->findApplicableDiscounts('ROOM', $roomId, $context, $code);
            $applicable = $applicable || !empty($roomDiscounts);
        }
        
        if (!$applicable) {
            return [
                'valid' => false,
                'message' => 'Bu indirim kodu seçilen otel veya oda için geçerli değil.'
            ];
        }
        
        // If all checks pass
        return [
            'valid' => true,
            'message' => 'Geçerli indirim kodu.',
            'discount' => $discount
        ];
    }
}