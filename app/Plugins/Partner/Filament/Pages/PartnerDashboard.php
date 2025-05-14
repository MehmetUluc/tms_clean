<?php

namespace App\Plugins\Partner\Filament\Pages;

use App\Models\User;
use App\Plugins\Partner\Models\Partner;
use App\Plugins\Partner\Models\PartnerTransaction;
use App\Plugins\Partner\Models\PartnerPaymentRequest;
use App\Plugins\Partner\Services\PartnerService;
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

class PartnerDashboard extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.partner-dashboard';

    protected static ?string $title = 'Partner Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Partner';

    public $partner;
    public $startDate;
    public $endDate;
    public $summary = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('partner');
    }

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

        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');

        $this->loadSummary();
    }

    public function loadSummary(): void
    {
        $partnerService = app(PartnerService::class);
        $this->summary = $partnerService->getFinancialSummary(
            $this->partner,
            $this->startDate,
            $this->endDate
        );
    }

    public function getStats(): array
    {
        $activeHotels = $this->partner->hotels()->where('is_active', true)->count();
        $totalHotels = $this->partner->hotels()->count();
        $totalRooms = DB::table('rooms')
            ->whereIn('hotel_id', $this->partner->hotels()->pluck('id'))
            ->count();
        $pendingPayments = $this->partner->paymentRequests()
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

            Card::make('Current Balance', number_format($this->summary['balance'] ?? 0, 2) . ' ' . config('partner.default_currency'))
                ->description('Available for payout')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Card::make('Pending Payments', number_format($pendingPayments, 2) . ' ' . config('partner.default_currency'))
                ->description($this->partner->paymentRequests()->where('status', 'pending')->count() . ' requests pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }

    public function getTransactions()
    {
        return PartnerTransaction::query()
            ->where('partner_id', $this->partner->id)
            ->whereBetween('transaction_date', [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay()
            ])
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();
    }

    public function getPartnerHotels()
    {
        return $this->partner->hotels()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->limit(5)
            ->get();
    }

    public function getPaymentRequests()
    {
        return PartnerPaymentRequest::query()
            ->where('partner_id', $this->partner->id)
            ->orderBy('requested_date', 'desc')
            ->limit(5)
            ->get();
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return PartnerTransaction::query()
            ->where('partner_id', $this->partner->id)
            ->whereBetween('transaction_date', [
                now()->subDays(30)->startOfDay(),
                now()->endOfDay()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PartnerTransaction::query()
                    ->where('partner_id', $this->partner->id)
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
                    ->money(fn () => config('partner.default_currency'))
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