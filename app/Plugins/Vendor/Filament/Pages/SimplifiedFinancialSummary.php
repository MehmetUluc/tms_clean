<?php

namespace App\Plugins\Vendor\Filament\Pages;

use App\Plugins\Vendor\Services\VendorService;
use Filament\Pages\Page;

class SimplifiedFinancialSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Vendor';
    
    protected static ?string $navigationLabel = 'Simplified Financial';
    
    protected static ?string $title = 'Simplified Financial Summary';
    
    protected static ?int $navigationSort = 104;
    
    protected static string $view = 'vendor::filament.pages.simplified-financial-summary';
    
    public $vendor;
    
    public function mount(): void
    {
        // Redirect if user is not a vendor
        if (!auth()->user()->hasRole('vendor')) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }

        $vendorService = app(VendorService::class);
        $this->vendor = $vendorService->getVendorForUser(auth()->user());

        if (!$this->vendor) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }
    }
}