<?php

namespace App\Plugins\Partner\Filament\Pages;

use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Plugins\Partner\Models\Partner;
use App\Plugins\Partner\Models\PartnerTransaction;
use App\Plugins\Partner\Services\PartnerService;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Booking\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class PartnerDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $slug = 'partner-dashboard';
    protected static ?string $title = 'Partner Dashboard';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.partner-dashboard';
    
    public ?Partner $partner = null;
    public array $stats = [];
    
    /**
     * Check if the user can access this page
     */
    public static function canAccess(): bool
    {
        return Auth::check() && 
               Auth::user()->isPartner() && 
               Auth::user()->can('view_own_partner_profile');
    }
    
    public function mount(): void
    {
        $this->partner = Auth::user()->getAssociatedPartner();
        
        if (!$this->partner) {
            // Eğer partner kaydı yoksa onboarding'e yönlendir
            redirect('/partner/partner-onboarding');
        }
        
        if (!$this->partner->onboarding_completed) {
            // Eğer onboarding tamamlanmamışsa onboarding'e yönlendir
            redirect('/partner/partner-onboarding');
        }
        
        $this->loadStats();
    }
    
    protected function loadStats(): void
    {
        $partnerService = app(PartnerService::class);
        $financialSummary = $partnerService->getFinancialSummary($this->partner);
        
        // Otel sayısı
        $hotelCount = Hotel::where('partner_id', $this->partner->id)->count();
        $activeHotelCount = Hotel::where('partner_id', $this->partner->id)
            ->where('is_active', true)
            ->count();
            
        // Bu ayki rezervasyonlar
        $monthlyReservations = Reservation::whereHas('hotel', function ($query) {
            $query->where('partner_id', $this->partner->id);
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
        
        // Bekleyen ödemeler
        $pendingPayments = $this->partner->paymentRequests()
            ->where('status', 'pending')
            ->sum('amount');
            
        $this->stats = [
            'hotels' => [
                'total' => $hotelCount,
                'active' => $activeHotelCount,
            ],
            'reservations' => [
                'monthly' => $monthlyReservations,
                'total' => $financialSummary['transactions']['bookings'],
            ],
            'financial' => [
                'balance' => $financialSummary['balance'],
                'pending_payments' => $pendingPayments,
                'monthly_revenue' => $financialSummary['transactions']['total_net_amount'],
                'commission_rate' => $this->partner->default_commission_rate,
            ],
        ];
    }
    
    
    protected function getViewData(): array
    {
        return [
            'stats' => $this->stats,
            'partner' => $this->partner,
        ];
    }
}