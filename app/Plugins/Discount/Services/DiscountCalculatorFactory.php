<?php

namespace App\Plugins\Discount\Services;

use App\Plugins\Discount\Contracts\DiscountCalculatorInterface;
use App\Plugins\Discount\Enums\DiscountType;
use App\Plugins\Discount\Services\Calculators\PercentageDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\FixedAmountDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\FreeNightsDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\NthNightFreeDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\EarlyBookingDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\LastMinuteDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\LongStayDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\PackageDealDiscountCalculator;
use Illuminate\Support\Facades\App;

class DiscountCalculatorFactory
{
    /**
     * Create a calculator for the given discount type
     *
     * @param DiscountType $type The discount type
     * @return DiscountCalculatorInterface
     * @throws \InvalidArgumentException If the discount type is not supported
     */
    public function createCalculator(DiscountType $type): DiscountCalculatorInterface
    {
        return match ($type) {
            DiscountType::PERCENTAGE => App::make(PercentageDiscountCalculator::class),
            DiscountType::FIXED_AMOUNT => App::make(FixedAmountDiscountCalculator::class),
            DiscountType::FREE_NIGHTS => App::make(FreeNightsDiscountCalculator::class),
            DiscountType::NTH_NIGHT_FREE => App::make(NthNightFreeDiscountCalculator::class),
            DiscountType::EARLY_BOOKING => App::make(EarlyBookingDiscountCalculator::class),
            DiscountType::LAST_MINUTE => App::make(LastMinuteDiscountCalculator::class),
            DiscountType::LONG_STAY => App::make(LongStayDiscountCalculator::class),
            DiscountType::PACKAGE_DEAL => App::make(PackageDealDiscountCalculator::class),
            default => throw new \InvalidArgumentException("Unsupported discount type: {$type->value}"),
        };
    }
}