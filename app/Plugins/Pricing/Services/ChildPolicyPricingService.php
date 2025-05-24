<?php

namespace App\Plugins\Pricing\Services;

use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\DailyRate;
use Illuminate\Support\Collection;

class ChildPolicyPricingService
{
    /**
     * Calculate price including child policies
     *
     * @param RatePlan $ratePlan
     * @param DailyRate $dailyRate
     * @param int $adults
     * @param array $childrenAges
     * @param int $nights
     * @return array
     */
    public function calculatePriceWithChildren(
        RatePlan $ratePlan,
        DailyRate $dailyRate,
        int $adults,
        array $childrenAges,
        int $nights = 1
    ): array {
        $room = $ratePlan->room;
        $hotel = $room->hotel;
        
        // Get child policy settings
        $childPolicy = $this->getEffectiveChildPolicy($hotel, $room);
        
        // Calculate base adult price
        $adultPrice = $this->calculateAdultPrice($dailyRate, $adults);
        
        // Calculate children prices
        $childrenPricing = $this->calculateChildrenPrices(
            $dailyRate,
            $childrenAges,
            $childPolicy,
            $adults
        );
        
        // Calculate totals
        $pricePerNight = $adultPrice + $childrenPricing['total'];
        $totalPrice = $pricePerNight * $nights;
        
        return [
            'adults_price' => $adultPrice,
            'children_price' => $childrenPricing['total'],
            'children_details' => $childrenPricing['details'],
            'price_per_night' => $pricePerNight,
            'total_price' => $totalPrice,
            'nights' => $nights,
            'free_children' => $childrenPricing['free_count'],
            'paid_children' => $childrenPricing['paid_count'],
            'currency' => $dailyRate->currency ?? 'TRY',
        ];
    }
    
    /**
     * Get effective child policy (room override or hotel default)
     */
    protected function getEffectiveChildPolicy(Hotel $hotel, Room $room): array
    {
        // Check if room has custom child policy
        if ($room->override_child_policy && $room->custom_child_policies) {
            return [
                'policies' => $room->custom_child_policies,
                'max_children' => $room->custom_max_children ?? 2,
                'age_limit' => $room->custom_child_age_limit ?? 12,
                'children_stay_free' => $hotel->children_stay_free ?? false,
            ];
        }
        
        // Use hotel default policy
        return [
            'policies' => $hotel->child_policies ?? [],
            'max_children' => $hotel->max_children_per_room ?? 2,
            'age_limit' => $hotel->child_age_limit ?? 12,
            'children_stay_free' => $hotel->children_stay_free ?? false,
        ];
    }
    
    /**
     * Calculate adult price based on daily rate
     */
    protected function calculateAdultPrice(DailyRate $dailyRate, int $adults): float
    {
        if ($dailyRate->is_per_person) {
            // Per person pricing
            $pricePerPerson = $dailyRate->getPriceForOccupancy($adults);
            return $pricePerPerson * $adults;
        } else {
            // Per room pricing
            return $dailyRate->base_price;
        }
    }
    
    /**
     * Calculate children prices based on policy
     */
    protected function calculateChildrenPrices(
        DailyRate $dailyRate,
        array $childrenAges,
        array $childPolicy,
        int $adults
    ): array {
        $totalChildrenPrice = 0;
        $details = [];
        $freeCount = 0;
        $paidCount = 0;
        
        // Sort children by age (youngest first for free policy)
        sort($childrenAges);
        
        foreach ($childrenAges as $index => $age) {
            $childPrice = $this->calculateSingleChildPrice(
                $dailyRate,
                $age,
                $index + 1,
                $childPolicy,
                $adults
            );
            
            $details[] = [
                'age' => $age,
                'price' => $childPrice,
                'is_free' => $childPrice == 0,
            ];
            
            if ($childPrice == 0) {
                $freeCount++;
            } else {
                $paidCount++;
            }
            
            $totalChildrenPrice += $childPrice;
        }
        
        return [
            'total' => $totalChildrenPrice,
            'details' => $details,
            'free_count' => $freeCount,
            'paid_count' => $paidCount,
        ];
    }
    
    /**
     * Calculate price for a single child
     */
    protected function calculateSingleChildPrice(
        DailyRate $dailyRate,
        int $age,
        int $childNumber,
        array $childPolicy,
        int $adults
    ): float {
        // Check if child exceeds age limit (counts as adult)
        if ($age > $childPolicy['age_limit']) {
            return $this->getAdultUnitPrice($dailyRate, $adults + 1);
        }
        
        // Check if children stay free
        if ($childPolicy['children_stay_free'] && $childNumber <= 1) {
            return 0; // First child is free
        }
        
        // Check specific age-based policies
        if (!empty($childPolicy['policies'])) {
            foreach ($childPolicy['policies'] as $policy) {
                if ($this->isPolicyApplicable($policy, $age, $childNumber)) {
                    return $this->applyChildPolicy($policy, $dailyRate, $adults);
                }
            }
        }
        
        // Default: charge 50% of adult price
        return $this->getAdultUnitPrice($dailyRate, $adults + 1) * 0.5;
    }
    
    /**
     * Check if a policy is applicable to a child
     */
    protected function isPolicyApplicable(array $policy, int $age, int $childNumber): bool
    {
        // Check age range
        if (isset($policy['age_from']) && $age < $policy['age_from']) {
            return false;
        }
        
        if (isset($policy['age_to']) && $age > $policy['age_to']) {
            return false;
        }
        
        // Check child order (e.g., first child, second child)
        if (isset($policy['child_order']) && $childNumber != $policy['child_order']) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Apply child policy to calculate price
     */
    protected function applyChildPolicy(array $policy, DailyRate $dailyRate, int $adults): float
    {
        $adultPrice = $this->getAdultUnitPrice($dailyRate, $adults + 1);
        
        // Check policy type
        if (isset($policy['is_free']) && $policy['is_free']) {
            return 0;
        }
        
        if (isset($policy['fixed_price'])) {
            return $policy['fixed_price'];
        }
        
        if (isset($policy['percentage'])) {
            return $adultPrice * ($policy['percentage'] / 100);
        }
        
        // Default to 50% if no specific policy
        return $adultPrice * 0.5;
    }
    
    /**
     * Get unit price for an adult
     */
    protected function getAdultUnitPrice(DailyRate $dailyRate, int $occupancy): float
    {
        if ($dailyRate->is_per_person) {
            return $dailyRate->getPriceForOccupancy($occupancy);
        } else {
            // For per-room pricing, divide by standard occupancy (usually 2)
            return $dailyRate->base_price / 2;
        }
    }
    
    /**
     * Validate if children count is within limits
     */
    public function validateChildrenCount(Hotel $hotel, Room $room, int $childrenCount): array
    {
        $policy = $this->getEffectiveChildPolicy($hotel, $room);
        
        if ($childrenCount > $policy['max_children']) {
            return [
                'valid' => false,
                'message' => "Maximum {$policy['max_children']} children allowed per room",
            ];
        }
        
        return [
            'valid' => true,
            'message' => null,
        ];
    }
}