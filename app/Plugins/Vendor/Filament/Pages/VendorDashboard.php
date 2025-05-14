<?php

namespace App\Plugins\Vendor\Filament\Pages;

use App\Models\User;
use App\Plugins\Vendor\Models\Vendor;
use App\Plugins\Vendor\Models\VendorTransaction;
use App\Plugins\Vendor\Models\VendorPaymentRequest;
use App\Plugins\Vendor\Services\VendorService;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Infolists\Infolist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class VendorDashboard extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.vendor-dashboard';

    protected static ?string $title = 'Vendor Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Vendor';

    public $vendor;
    public $startDate;
    public $endDate;
    public $summary = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('vendor');
    }

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

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        
        $this->loadSummary();
    }

    public function loadSummary(): void
    {
        $vendorService = app(VendorService::class);
        $this->summary = $vendorService->getFinancialSummary(
            $this->vendor, 
            $this->startDate, 
            $this->endDate
        );
    }

    public function getStats(): array
    {
        $activeHotels = $this->vendor->hotels()->where('is_active', true)->count();
        $totalHotels = $this->vendor->hotels()->count();
        $totalRooms = DB::table('rooms')
            ->whereIn('hotel_id', $this->vendor->hotels()->pluck('id'))
            ->count();
        $pendingPayments = $this->vendor->paymentRequests()
            ->where('status', 'pending')
            ->sum('amount');

        return [
            Card::make('Active Hotels', $activeHotels)
                ->description($totalHotels > 0 ? round(($activeHotels / $totalHotels) * 100) . '% of total' : '0% of total')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),

            Card::make('Total Rooms', $totalRooms)
                ->description('Across all hotels')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary'),

            Card::make('Current Balance', number_format($this->summary['balance'] ?? 0, 2) . ' ' . config('vendor.default_currency'))
                ->description('Available for payout')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Card::make('Pending Payments', number_format($pendingPayments, 2) . ' ' . config('vendor.default_currency'))
                ->description($this->vendor->paymentRequests()->where('status', 'pending')->count() . ' requests pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }

    public function getTransactions()
    {
        return VendorTransaction::query()
            ->where('vendor_id', $this->vendor->id)
            ->whereBetween('transaction_date', [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay()
            ])
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();
    }

    public function getVendorHotels()
    {
        return $this->vendor->hotels()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->limit(5)
            ->get();
    }

    public function getPaymentRequests()
    {
        return VendorPaymentRequest::query()
            ->where('vendor_id', $this->vendor->id)
            ->orderBy('requested_date', 'desc')
            ->limit(5)
            ->get();
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return VendorTransaction::query()
            ->where('vendor_id', $this->vendor->id)
            ->whereBetween('transaction_date', [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                VendorTransaction::query()
                    ->where('vendor_id', $this->vendor->id)
                    ->whereBetween('transaction_date', [
                        now()->subDays(30)->startOfDay(),
                        now()->endOfDay()
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Hotel')
                    ->searchable(),

                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'primary' => 'booking',
                        'danger' => 'cancellation',
                        'warning' => 'modification',
                        'success' => 'payment',
                        'gray' => 'other',
                    ]),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn () => config('vendor.default_currency'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'processed',
                        'danger' => 'cancelled',
                        'gray' => 'failed',
                    ]),
            ]);
    }
}