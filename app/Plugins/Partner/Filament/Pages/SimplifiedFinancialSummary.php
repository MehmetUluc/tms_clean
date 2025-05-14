<?php

namespace App\Plugins\Partner\Filament\Pages;

use App\Plugins\Partner\Services\PartnerService;
use Filament\Pages\Page;

class SimplifiedFinancialSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Partner';
    
    protected static ?string $navigationLabel = 'Simplified Financial';
    
    protected static ?string $title = 'Simplified Financial Summary';
    
    protected static ?int $navigationSort = 104;
    
    protected static string $view = 'partner::filament.pages.simplified-financial-summary';
    
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