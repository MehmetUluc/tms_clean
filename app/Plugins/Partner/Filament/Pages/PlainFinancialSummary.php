<?php

namespace App\Plugins\Partner\Filament\Pages;

use App\Plugins\Partner\Services\PartnerService;
use Filament\Pages\Page;

class PlainFinancialSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Partner';
    
    protected static ?string $navigationLabel = 'Plain Financial Summary';
    
    protected static ?string $title = 'Plain Financial Summary';
    
    protected static ?int $navigationSort = 107;
    
    // Explicitly use the partner namespaced view
    protected static string $view = 'partner::filament.pages.plain-financial-summary';
    
    public $partner;
    
    public function mount(): void
    {
        // Redirect if user is not a partner
        if (!auth()->user()->hasRole('partner')) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }

        $partnerService = app(PartnerService::class);
        $this->partner = $partnerService->getPartnerForUser(auth()->user());

        if (!$this->partner) {
            $this->redirect(route('filament.admin.pages.dashboard'));
            return;
        }
    }
}