<?php

namespace App\Plugins\Partner\Filament\Resources\PartnerResource\Pages;

use App\Plugins\Partner\Filament\Resources\PartnerResource;
use App\Plugins\Partner\Models\PartnerTransaction;
use App\Plugins\Partner\Models\PartnerPaymentRequest;
use App\Plugins\Partner\Models\PartnerPayment;
use App\Plugins\Partner\Services\PartnerService;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Card;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ViewPartnerFinancials extends Page
{
    protected static string $resource = PartnerResource::class;

    protected static ?string $title = 'Financial Summary';

    protected static string $view = 'filament.pages.view-partner-financials';

    public $partner;
    public $startDate;
    public $endDate;
    public $summary = [];
    public $activeTab = 'transactions';

    public function mount($record): void
    {
        $this->partner = $record;
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

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
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
                                ->submit()
                                ->icon('heroicon-o-funnel')
                                ->color('primary')
                                ->action(function (array $data) {
                                    $this->startDate = $data['startDate'];
                                    $this->endDate = $data['endDate'];
                                    $this->loadSummary();
                                }),
                        ]),
                    ]),
            ]);
    }

    public function summaryInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Financial Summary')
                    ->columns(3)
                    ->schema([
                        Card::make()
                            ->schema([
                                TextEntry::make('period')
                                    ->label('Period')
                                    ->formatStateUsing(fn () => date('d M Y', strtotime($this->startDate)) . ' - ' . date('d M Y', strtotime($this->endDate))),
                                
                                TextEntry::make('balance')
                                    ->label('Current Balance')
                                    ->formatStateUsing(fn () => number_format($this->summary['balance'] ?? 0, 2) . ' ' . config('partner.default_currency'))
                                    ->size('xl')
                                    ->weight('bold')
                                    ->color(fn () => $this->summary['balance'] > 0 ? 'success' : 'danger'),
                            ]),
                        
                        Card::make()
                            ->schema([
                                TextEntry::make('transactions')
                                    ->label('Transactions')
                                    ->formatStateUsing(fn () => number_format($this->summary['transactions']['total_count'] ?? 0) . ' transactions'),
                                
                                TextEntry::make('total_amount')
                                    ->label('Total Amount')
                                    ->formatStateUsing(fn () => number_format($this->summary['transactions']['total_amount'] ?? 0, 2) . ' ' . config('partner.default_currency'))
                                    ->size('xl')
                                    ->weight('bold'),
                                
                                TextEntry::make('total_commission')
                                    ->label('Total Commission')
                                    ->formatStateUsing(fn () => number_format($this->summary['transactions']['total_commission'] ?? 0, 2) . ' ' . config('partner.default_currency'))
                                    ->color('danger'),
                                
                                TextEntry::make('total_net_amount')
                                    ->label('Total Net Amount')
                                    ->formatStateUsing(fn () => number_format($this->summary['transactions']['total_net_amount'] ?? 0, 2) . ' ' . config('partner.default_currency'))
                                    ->color('success'),
                            ]),
                        
                        Card::make()
                            ->schema([
                                TextEntry::make('payments')
                                    ->label('Payments')
                                    ->formatStateUsing(fn () => number_format($this->summary['payments']['total_payment_requests'] ?? 0) . ' payment requests'),
                                
                                TextEntry::make('total_payments')
                                    ->label('Total Payments')
                                    ->formatStateUsing(fn () => number_format($this->summary['payments']['total_payments'] ?? 0, 2) . ' ' . config('partner.default_currency'))
                                    ->size('xl')
                                    ->weight('bold')
                                    ->color('success'),
                                
                                TextEntry::make('pending_payment_requests')
                                    ->label('Pending Payment Requests')
                                    ->formatStateUsing(fn () => number_format($this->summary['payments']['pending_payment_requests'] ?? 0) . ' (' . number_format($this->summary['payments']['pending_payment_amount'] ?? 0, 2) . ' ' . config('partner.default_currency') . ')'),
                            ]),
                    ]),
            ]);
    }

    public function transactionsTable(Table $table): Table
    {
        return $table
            ->query(
                PartnerTransaction::query()
                    ->where('partner_id', $this->partner->id)
                    ->whereBetween('transaction_date', [
                        $this->startDate . ' 00:00:00',
                        $this->endDate . ' 23:59:59'
                    ])
            )
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
            ->bulkActions([
                //
            ]);
    }

    public function paymentRequestsTable(Table $table): Table
    {
        return $table
            ->query(
                PartnerPaymentRequest::query()
                    ->where('partner_id', $this->partner->id)
                    ->whereBetween('requested_date', [
                        $this->startDate . ' 00:00:00',
                        $this->endDate . ' 23:59:59'
                    ])
            )
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
                
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PartnerPaymentRequest $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->maxLength(500),
                    ])
                    ->action(function (PartnerPaymentRequest $record, array $data): void {
                        $partnerService = app(PartnerService::class);
                        $partnerService->processPayment(
                            $record,
                            'approved',
                            null,
                            null,
                            $data['notes'] ?? null
                        );
                        
                        Notification::make()
                            ->title('Payment request approved')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (PartnerPaymentRequest $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (PartnerPaymentRequest $record, array $data): void {
                        $record->reject(auth()->id(), $data['rejection_reason']);
                        
                        Notification::make()
                            ->title('Payment request rejected')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->visible(fn (VendorPaymentRequest $record): bool => $record->status === 'approved')
                    ->form([
                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Payment Reference')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\DateTimePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required()
                            ->default(now()),
                        
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(config('partner.payment_methods'))
                            ->required()
                            ->default('bank_transfer'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->maxLength(500),
                    ])
                    ->action(function (PartnerPaymentRequest $record, array $data): void {
                        $partnerService = app(PartnerService::class);
                        $partnerService->processPayment(
                            $record,
                            'paid',
                            $data['payment_method'],
                            $data['payment_reference'],
                            $data['notes'] ?? null
                        );
                        
                        Notification::make()
                            ->title('Payment request marked as paid')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }

    public function paymentsTable(Table $table): Table
    {
        return $table
            ->query(
                PartnerPayment::query()
                    ->where('partner_id', $this->partner->id)
                    ->whereBetween('payment_date', [
                        $this->startDate . ' 00:00:00',
                        $this->endDate . ' 23:59:59'
                    ])
            )
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
            ->bulkActions([
                //
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToPartner')
                ->label('Back to Partner')
                ->url(fn () => PartnerResource::getUrl('view', ['record' => $this->partner])),
            
            Actions\Action::make('createPaymentRequest')
                ->label('Create Payment Request')
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