<?php

namespace App\Plugins\Pricing\Filament\Resources\RatePlanResource\Pages;

use App\Plugins\Pricing\Filament\Resources\RatePlanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRatePlan extends CreateRecord
{
    protected static string $resource = RatePlanResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Add current user ID
        $data['created_by'] = auth()->id();
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}