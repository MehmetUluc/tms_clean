<?php

namespace App\Plugins\Partner\Filament\Pages;

use App\Plugins\Partner\Models\PartnerTransaction;
use App\Plugins\Partner\Models\PartnerPaymentRequest;
use App\Plugins\Partner\Models\PartnerPayment;
use App\Plugins\Partner\Services\PartnerService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Card;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class FinancialSummary extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithInfolists;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'partner::filament.pages.financial-summary';

    protected static ?string $title = 'Financial Summary';

    protected static ?string $navigationGroup = 'Partner';

    protected static ?int $navigationSort = 20;

    protected static string $pollingInterval = '0'; // Disable polling for better performance

    public $partner;
    public $startDate;
    public $endDate;
    public $summary = [];
    public $activeTab = 'transactions';
    public $filterData = [];
    public $chartData = [];
    public $monthlyBreakdown = [];
    public $transactionTypeBreakdown = [];

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

        $this->filterData = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

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

        $this->generateChartData();
        $this->generateMonthlyBreakdown();
        $this->generateTransactionTypeBreakdown();

        // Notify frontend to update charts
        $this->dispatch('chartDataUpdated');
    }

    protected function generateChartData(): void
    {
        // Get transactions for chart
        $transactions = PartnerTransaction::where('partner_id', $this->partner->id)
            ->whereBetween('transaction_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->orderBy('transaction_date')
            ->get();

        if ($transactions->isEmpty()) {
            $this->chartData = [];
            return;
        }

        // Group by date
        $dateFormat = 'Y-m-d';
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // If period is longer than 31 days, group by month instead of day
        if ($startDate->diffInDays($endDate) > 31) {
            $dateFormat = 'Y-m';
        }

        $groupedData = $transactions->groupBy(function ($transaction) use ($dateFormat) {
            return Carbon::parse($transaction->transaction_date)->format($dateFormat);
        });

        // Create datepoints for the entire period
        $datePoints = [];
        $amountData = [];
        $netAmountData = [];

        // Fill in all dates in the range
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);

        foreach ($period as $date) {
            $formattedDate = $date->format($dateFormat);

            if (!isset($datePoints[$formattedDate])) {
                $datePoints[$formattedDate] = [
                    'date' => $formattedDate,
                    'amount' => 0,
                    'net_amount' => 0,
                ];
            }
        }

        // Fill in actual transaction data
        foreach ($groupedData as $date => $transactionsForDate) {
            $totalAmount = $transactionsForDate->sum('amount');
            $totalNetAmount = $transactionsForDate->sum('net_amount');

            $datePoints[$date] = [
                'date' => $date,
                'amount' => $totalAmount,
                'net_amount' => $totalNetAmount,
            ];
        }

        // Sort by date
        ksort($datePoints);

        // Prepare data arrays for chart
        $dates = [];
        $amounts = [];
        $netAmounts = [];

        foreach ($datePoints as $point) {
            $dates[] = $point['date'];
            $amounts[] = round($point['amount'], 2);
            $netAmounts[] = round($point['net_amount'], 2);
        }

        $this->chartData = [
            'dates' => $dates,
            'amounts' => $amounts,
            'netAmounts' => $netAmounts,
        ];
    }

    protected function generateMonthlyBreakdown(): void
    {
        // Get monthly breakdown of transactions
        $transactions = PartnerTransaction::where('partner_id', $this->partner->id)
            ->whereBetween('transaction_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->get();

        if ($transactions->isEmpty()) {
            $this->monthlyBreakdown = [];
            return;
        }

        // Group by month
        $monthlyData = $transactions->groupBy(function ($transaction) {
            return Carbon::parse($transaction->transaction_date)->format('Y-m');
        });

        $formattedData = [];

        foreach ($monthlyData as $month => $transactionsForMonth) {
            $formattedMonth = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $totalAmount = $transactionsForMonth->sum('amount');
            $totalCommission = $transactionsForMonth->sum('commission_amount');
            $totalNetAmount = $transactionsForMonth->sum('net_amount');

            $formattedData[] = [
                'month' => $formattedMonth,
                'amount' => round($totalAmount, 2),
                'commission' => round($totalCommission, 2),
                'net_amount' => round($totalNetAmount, 2),
            ];
        }

        // Sort data by month
        usort($formattedData, function ($a, $b) {
            return Carbon::createFromFormat('M Y', $a['month'])->timestamp -
                   Carbon::createFromFormat('M Y', $b['month'])->timestamp;
        });

        $this->monthlyBreakdown = $formattedData;
    }

    protected function generateTransactionTypeBreakdown(): void
    {
        // Get breakdown by transaction type
        $transactions = PartnerTransaction::where('partner_id', $this->partner->id)
            ->whereBetween('transaction_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ])
            ->get();

        if ($transactions->isEmpty()) {
            $this->transactionTypeBreakdown = [];
            return;
        }

        // Group by transaction type
        $typesData = $transactions->groupBy('transaction_type');

        $formattedData = [];
        $total = $transactions->sum('amount');

        foreach ($typesData as $type => $transactionsOfType) {
            $count = $transactionsOfType->count();
            $amount = $transactionsOfType->sum('amount');
            $percentage = $total > 0 ? ($amount / $total) * 100 : 0;

            $formattedData[] = [
                'type' => ucfirst($type),
                'count' => $count,
                'amount' => round($amount, 2),
                'percentage' => round($percentage, 2),
            ];
        }

        // Sort by amount
        usort($formattedData, function ($a, $b) {
            return $b['amount'] - $a['amount'];
        });

        $this->transactionTypeBreakdown = $formattedData;
    }

    #[On('changeTab')]
    public function changeTab($data): void
    {
        $this->activeTab = $data['tab'];

        // Tabloyu güncelle
        $this->resetTableFiltersForm();
    }

    public function dateFilter(): Forms\Components\Component
    {
        return Forms\Components\Grid::make(3)
            ->schema([
                Forms\Components\DatePicker::make('startDate')
                    ->label('Start Date')
                    ->required()
                    ->default(now()->startOfMonth()),

                Forms\Components\DatePicker::make('endDate')
                    ->label('End Date')
                    ->required()
                    ->default(now()->endOfMonth())
                    ->after('startDate'),

                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('filter')
                        ->label('Apply Filter')
                        ->icon('heroicon-o-funnel')
                        ->color('primary')
                        ->action(function () {
                            // Get form data
                            $this->startDate = $this->filterData['startDate'];
                            $this->endDate = $this->filterData['endDate'];
                            // Update data
                            $this->loadSummary();

                            // Show notification
                            \Filament\Notifications\Notification::make()
                                ->title('Date filter applied')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('filterData')
            ->schema([
                $this->dateFilter(),
            ]);
    }

    protected function generateTransactionTypeBreakdownEntries(): array
    {
        $entries = [];

        if (empty($this->transactionTypeBreakdown)) {
            return [
                TextEntry::make('no_data')
                    ->state('No transaction data available for the selected period')
                    ->size(TextEntry\TextEntrySize::Small)
                    ->color('gray'),
            ];
        }

        foreach ($this->transactionTypeBreakdown as $index => $typeData) {
            $entries[] = TextEntry::make("type_{$index}")
                ->label($typeData['type'])
                ->state(function () use ($typeData) {
                    $barLength = 15;
                    $percentageBar = str_repeat('■', min(round($typeData['percentage'] / 10), $barLength));

                    return "{$percentageBar} {$typeData['percentage']}% ({$typeData['count']} txns)";
                })
                ->inlineLabel()
                ->color(function () use ($typeData) {
                    switch (strtolower($typeData['type'])) {
                        case 'booking': return 'success';
                        case 'cancellation': return 'danger';
                        case 'modification': return 'warning';
                        case 'payment': return 'info';
                        case 'refund': return 'danger';
                        default: return 'gray';
                    }
                });
        }

        return $entries;
    }

    public function summaryInfolist(Infolist $infolist): Infolist
    {
        $currency = config('partner.default_currency');
        $balance = $this->summary['balance'] ?? 0;
        $totalTransactions = $this->summary['transactions']['total_count'] ?? 0;
        $totalAmount = $this->summary['transactions']['total_amount'] ?? 0;
        $totalCommission = $this->summary['transactions']['total_commission'] ?? 0;
        $totalNetAmount = $this->summary['transactions']['total_net_amount'] ?? 0;
        $pendingPaymentRequests = $this->summary['payments']['pending_payment_requests'] ?? 0;
        $pendingAmount = $this->summary['payments']['pending_payment_amount'] ?? 0;
        $totalPayments = $this->summary['payments']['total_payments'] ?? 0;

        // Calculate commission percentage for visualization
        $commissionPercentage = $totalAmount > 0 ? ($totalCommission / $totalAmount) * 100 : 0;

        return $infolist
            ->state($this->summary)
            ->schema([
                // Current Balance Card
                Card::make()
                    ->extraAttributes(['class' => 'bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border-t-4 border-indigo-500 shadow-md hover:shadow-lg transition-shadow rounded-xl p-4'])
                    ->schema([
                        TextEntry::make('balance_heading')
                            ->label('Current Balance')
                            ->state('Available funds')
                            ->size(TextEntry\TextEntrySize::Small)
                            ->weight(FontWeight::Medium)
                            ->color('gray'),

                        TextEntry::make('balance')
                            ->state(number_format($balance, 2) . ' ' . $currency)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->color($balance > 0 ? 'success' : 'danger')
                            ->icon($balance > 0 ? 'heroicon-o-banknotes' : 'heroicon-o-exclamation-circle')
                            ->iconPosition(IconPosition::Before),

                        TextEntry::make('commission_percentage')
                            ->state('Commission rate: ' . number_format($commissionPercentage, 2) . '%')
                            ->size(TextEntry\TextEntrySize::Small)
                            ->color('gray')
                            ->visible($totalAmount > 0),
                    ]),

                // Revenue Stats Card
                Card::make()
                    ->extraAttributes(['class' => 'bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border-t-4 border-emerald-500 shadow-md hover:shadow-lg transition-shadow rounded-xl p-4'])
                    ->schema([
                        TextEntry::make('revenue_stats')
                            ->label('Revenue')
                            ->state($totalTransactions . ' transactions')
                            ->size(TextEntry\TextEntrySize::Small)
                            ->weight(FontWeight::Medium)
                            ->color('gray')
                            ->icon('heroicon-o-currency-dollar')
                            ->iconPosition(IconPosition::Before),

                        TextEntry::make('total_amount')
                            ->label('Gross Revenue')
                            ->state(number_format($totalAmount, 2) . ' ' . $currency)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('primary'),

                        TextEntry::make('revenue_chart')
                            ->state(function () use ($totalAmount, $totalCommission, $totalNetAmount) {
                                if ($totalAmount <= 0) return 'No revenue data';

                                // Create a mini visualization of revenue breakdown
                                $netPercentage = ($totalNetAmount / $totalAmount) * 100;
                                $commissionPercentage = ($totalCommission / $totalAmount) * 100;

                                $barLength = 20; // Characters long
                                $netChars = round(($netPercentage / 100) * $barLength);
                                $commissionChars = $barLength - $netChars;

                                $netBar = str_repeat('▓', $netChars);
                                $commissionBar = str_repeat('░', $commissionChars);

                                return $netBar . $commissionBar . ' ' . number_format($netPercentage, 1) . '% net';
                            })
                            ->size(TextEntry\TextEntrySize::Small)
                            ->weight(FontWeight::Light)
                            ->color('gray'),
                    ]),

                // Payment Stats Card
                Card::make()
                    ->extraAttributes(['class' => 'bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 border-t-4 border-amber-500 shadow-md hover:shadow-lg transition-shadow rounded-xl p-4'])
                    ->schema([
                        TextEntry::make('payment_status')
                            ->label('Payments')
                            ->state(function () use ($pendingPaymentRequests) {
                                return $pendingPaymentRequests > 0
                                    ? $pendingPaymentRequests . ' pending requests'
                                    : 'All payments settled';
                            })
                            ->size(TextEntry\TextEntrySize::Small)
                            ->weight(FontWeight::Medium)
                            ->color('gray')
                            ->icon('heroicon-o-credit-card')
                            ->iconPosition(IconPosition::Before),

                        TextEntry::make('received_payments')
                            ->label('Total Received')
                            ->state(number_format($totalPayments, 2) . ' ' . $currency)
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('success'),

                        TextEntry::make('pending_payment_amount')
                            ->label('Pending Amount')
                            ->state(function () use ($pendingAmount, $currency) {
                                if ($pendingAmount <= 0) return 'No pending amount';
                                return number_format($pendingAmount, 2) . ' ' . $currency;
                            })
                            ->size(TextEntry\TextEntrySize::Small)
                            ->weight(FontWeight::Medium)
                            ->color($pendingAmount > 0 ? 'warning' : 'gray'),
                    ]),

                // Revenue Chart Section - Full Width Row
                Card::make()
                    ->extraAttributes(['class' => 'col-span-1 lg:col-span-3 bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden'])
                    ->heading('Revenue Trends')
                    ->schema([
                        TextEntry::make('revenue_chart_container')
                            ->state('Loading chart...')
                            ->extraAttributes([
                                'class' => 'h-80',
                                'id' => 'revenue-chart-container',
                                'wire:ignore' => true,
                            ]),
                    ]),

                // Financial Analysis Card
                Card::make()
                    ->heading('Earnings Analysis')
                    ->extraAttributes(['class' => 'bg-white dark:bg-gray-800 shadow-sm rounded-xl border-l-4 border-indigo-500'])
                    ->schema([
                        TextEntry::make('gross_revenue')
                            ->label('Gross Revenue')
                            ->state(number_format($totalAmount, 2) . ' ' . $currency)
                            ->inlineLabel()
                            ->weight(FontWeight::Bold),

                        TextEntry::make('commission_deduction')
                            ->label('Platform Commission')
                            ->state(number_format($totalCommission, 2) . ' ' . $currency . ' (' . number_format($commissionPercentage, 2) . '%)')
                            ->inlineLabel()
                            ->color('danger'),

                        TextEntry::make('separator')
                            ->state('───────────────────────')
                            ->size(TextEntry\TextEntrySize::Small)
                            ->color('gray'),

                        TextEntry::make('net_earnings')
                            ->label('Net Earnings')
                            ->state(number_format($totalNetAmount, 2) . ' ' . $currency)
                            ->inlineLabel()
                            ->weight(FontWeight::Bold)
                            ->color('success'),

                        TextEntry::make('earnings_trend')
                            ->state(function () use ($totalNetAmount, $totalAmount) {
                                if ($totalAmount <= 0) return 'No data available';

                                $netPercentage = ($totalNetAmount / $totalAmount) * 100;
                                return 'You keep ' . number_format($netPercentage, 2) . '% of your gross revenue';
                            })
                            ->size(TextEntry\TextEntrySize::Small)
                            ->color('gray'),
                    ]),

                // Payment Status Card
                Card::make()
                    ->heading('Payment Status')
                    ->extraAttributes(['class' => 'bg-white dark:bg-gray-800 shadow-sm rounded-xl border-l-4 border-emerald-500'])
                    ->schema([
                        TextEntry::make('total_payments_received')
                            ->label('Total Payments Received')
                            ->state(number_format($totalPayments, 2) . ' ' . $currency)
                            ->inlineLabel()
                            ->weight(FontWeight::Bold),

                        TextEntry::make('pending_requests')
                            ->label('Pending Payment Requests')
                            ->state(function () use ($pendingPaymentRequests, $pendingAmount, $currency) {
                                if ($pendingPaymentRequests <= 0) return 'No pending requests';
                                return $pendingPaymentRequests . ' requests (' . number_format($pendingAmount, 2) . ' ' . $currency . ')';
                            })
                            ->inlineLabel()
                            ->color($pendingPaymentRequests > 0 ? 'warning' : 'gray'),

                        TextEntry::make('separator_2')
                            ->state('───────────────────────')
                            ->size(TextEntry\TextEntrySize::Small)
                            ->color('gray'),

                        TextEntry::make('payment_status')
                            ->label('Current Status')
                            ->state(function () use ($pendingPaymentRequests, $pendingAmount, $balance) {
                                if ($pendingPaymentRequests <= 0 && $balance <= 0) {
                                    return 'All settled';
                                } elseif ($pendingPaymentRequests > 0) {
                                    return 'Awaiting approval for ' . $pendingPaymentRequests . ' requests';
                                } else {
                                    return 'Available for withdrawal: ' . number_format($balance, 2);
                                }
                            })
                            ->inlineLabel()
                            ->weight(FontWeight::Medium)
                            ->color(function () use ($pendingPaymentRequests, $balance) {
                                if ($pendingPaymentRequests > 0) return 'warning';
                                if ($balance > 0) return 'success';
                                return 'gray';
                            }),
                    ]),

                // Transaction Type Chart
                Card::make()
                    ->heading('Transaction Types')
                    ->extraAttributes(['class' => 'col-span-1 lg:col-span-1 bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden'])
                    ->schema([
                        TextEntry::make('transaction_type_chart')
                            ->state('Loading chart...')
                            ->extraAttributes([
                                'class' => 'h-72',
                                'id' => 'transaction-type-chart',
                                'wire:ignore' => true,
                            ]),
                    ]),

                // Transaction Types Breakdown
                Card::make()
                    ->heading('Transaction Breakdown')
                    ->extraAttributes(['class' => 'col-span-1 lg:col-span-1 bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden p-4'])
                    ->schema($this->generateTransactionTypeBreakdownEntries()),

                // Monthly Breakdown Table
                Card::make()
                    ->heading('Monthly Performance')
                    ->extraAttributes(['class' => 'col-span-1 lg:col-span-1 bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden p-4'])
                    ->schema([
                        TextEntry::make('monthly_table')
                            ->state('')
                            ->extraAttributes([
                                'id' => 'monthly-breakdown-table',
                                'class' => 'monthly-breakdown-table overflow-x-auto',
                                'wire:ignore' => true,
                            ]),
                    ]),
            ]);
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Aktif sekmeye göre tablonun veri kaynağını değiştir
        if ($this->activeTab === 'transactions') {
            return PartnerTransaction::query()
                ->where('partner_id', $this->partner->id)
                ->whereBetween('transaction_date', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ]);
        } elseif ($this->activeTab === 'payment_requests') {
            return PartnerPaymentRequest::query()
                ->where('partner_id', $this->partner->id)
                ->whereBetween('requested_date', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ]);
        } elseif ($this->activeTab === 'payments') {
            return PartnerPayment::query()
                ->where('partner_id', $this->partner->id)
                ->whereBetween('payment_date', [
                    $this->startDate . ' 00:00:00',
                    $this->endDate . ' 23:59:59'
                ]);
        }

        // Varsayılan olarak işlemler tablosunu döndür
        return PartnerTransaction::query()
            ->where('partner_id', $this->partner->id)
            ->whereBetween('transaction_date', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59'
            ]);
    }

    public function table(Table $table): Table
    {
        $table = $table
            ->query($this->getTableQuery());

        // Aktif sekmeye göre tablo yapılandırmasını değiştir
        if ($this->activeTab === 'transactions') {
            $table = $this->configureTransactionsTable($table);
        } elseif ($this->activeTab === 'payment_requests') {
            $table = $this->configurePaymentRequestsTable($table);
        } elseif ($this->activeTab === 'payments') {
            $table = $this->configurePaymentsTable($table);
        } else {
            // Varsayılan olarak işlemler tablosunu yapılandır
            $table = $this->configureTransactionsTable($table);
        }

        return $table;
    }

    protected function configureTransactionsTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
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
                
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Hotel')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn () => config('partner.default_currency'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->money(fn () => config('partner.default_currency'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net Amount')
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->options([
                        'booking' => 'Booking',
                        'cancellation' => 'Cancellation',
                        'modification' => 'Modification',
                        'payment' => 'Payment',
                        'refund' => 'Refund',
                        'adjustment' => 'Adjustment',
                        'other' => 'Other',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processed' => 'Processed',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->paginated(true);
    }

    protected function configurePaymentRequestsTable(Table $table): Table
    {
        return $table
            
            ->columns([
                Tables\Columns\TextColumn::make('requested_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn () => config('partner.default_currency'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('bankAccount.bank_name')
                    ->label('Bank')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('bankAccount.account_name')
                    ->label('Account')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'primary' => 'paid',
                        'gray' => 'cancelled',
                    ]),
                
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->paginated(true);
    }

    protected function configurePaymentsTable(Table $table): Table
    {
        return $table
            
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_reference')
                    ->label('Reference')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn () => config('partner.default_currency'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                
                Tables\Columns\TextColumn::make('bankAccount.bank_name')
                    ->label('Bank')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'gray' => 'failed',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'failed' => 'Failed',
                    ]),
                
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(array_map(
                        fn ($method) => ucfirst(str_replace('_', ' ', $method)),
                        array_keys(config('partner.payment_methods'))
                    )),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->paginated(true);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createPaymentRequest')
                ->label('Request Payment')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->form([
                    Forms\Components\TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    
                    Forms\Components\Select::make('bank_account_id')
                        ->label('Bank Account')
                        ->options(function () {
                            return $this->partner->bankAccounts()
                                ->get()
                                ->pluck('account_name', 'id')
                                ->toArray();
                        })
                        ->required(),
                    
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->maxLength(500),
                ])
                ->action(function (array $data): void {
                    $partnerService = app(PartnerService::class);
                    $partnerService->createPaymentRequest(
                        $this->partner,
                        $data['amount'],
                        $data['bank_account_id'],
                        config('partner.default_currency'),
                        $data['notes'] ?? null
                    );
                    
                    Notification::make()
                        ->title('Payment request created')
                        ->success()
                        ->send();
                    
                    $this->loadSummary();
                }),
        ];
    }
}