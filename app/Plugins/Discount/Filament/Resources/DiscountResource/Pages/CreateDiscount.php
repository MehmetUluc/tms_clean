<?php

namespace App\Plugins\Discount\Filament\Resources\DiscountResource\Pages;

use App\Plugins\Discount\Filament\Resources\DiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDiscount extends CreateRecord
{
    protected static string $resource = DiscountResource::class;
}