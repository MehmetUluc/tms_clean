<?php

namespace App\Plugins\Pricing\Filament\Resources\RatePlanResource\Pages;

use App\Plugins\Pricing\Filament\Resources\RatePlanResource;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\DailyRate;
use App\Plugins\Pricing\Models\OccupancyRate;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Livewire\WithFileUploads;

class ManageRatePlanPrices extends Page
{
    use WithFileUploads;
    protected static string $resource = RatePlanResource::class;

    protected static string $view = 'vendor.pricing.rate-plans.manage-prices';
    
    public ?RatePlan $record = null;
    
    public ?array $priceData = [];
    
    public ?array $dateRange = [];
    
    public ?string $priceType = 'daily';
    
    public ?array $bulkPriceForm = [];
    
    public function mount(RatePlan $record): void
    {
        $this->record = $record;
        
        // Default date range to next 30 days
        $this->dateRange = [
            'start' => Carbon::today()->format('Y-m-d'),
            'end' => Carbon::today()->addDays(30)->format('Y-m-d'),
        ];
        
        $this->priceType = $record->occupancy_pricing ? 'occupancy' : 'daily';
        
        $this->loadPriceData();
    }
    
    public function dateRangeForm(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('dateRange.start')
                            ->label('Başlangıç Tarihi')
                            ->native(false)
                            ->required(),
                            
                        DatePicker::make('dateRange.end')
                            ->label('Bitiş Tarihi')
                            ->native(false)
                            ->required(),
                        
                        \Filament\Forms\Components\Actions::make([
                            \Filament\Forms\Components\Actions\Action::make('apply')
                                ->label('Uygula')
                                ->color('primary')
                                ->button()
                                ->action(function () {
                                    $this->loadPriceData();
                                }),
                        ])
                        ->fullWidth(),
                    ])
                    ->columns(1)
            ]);
    }
    
    public function loadPriceData(): void
    {
        if (empty($this->dateRange['start']) || empty($this->dateRange['end'])) {
            return;
        }
        
        $startDate = Carbon::parse($this->dateRange['start']);
        $endDate = Carbon::parse($this->dateRange['end']);
        
        if ($this->priceType === 'daily') {
            $this->loadDailyRates($startDate, $endDate);
        } else {
            $this->loadOccupancyRates($startDate, $endDate);
        }
    }
    
    protected function loadDailyRates(Carbon $startDate, Carbon $endDate): void
    {
        // Get existing daily rates for the date range
        $existingRates = DailyRate::where('rate_plan_id', $this->record->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function ($rate) {
                return $rate->date->format('Y-m-d');
            });
            
        // Create data array for form
        $this->priceData = [];
        
        // Loop through each date in the range
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $dateString = $date->format('Y-m-d');
            $dayOfWeek = $date->dayOfWeek;
            $dayName = match($dayOfWeek) {
                0 => 'Pazar',
                1 => 'Pazartesi',
                2 => 'Salı',
                3 => 'Çarşamba',
                4 => 'Perşembe',
                5 => 'Cuma',
                6 => 'Cumartesi',
                default => '',
            };
            
            // Check if we have an existing rate for this date
            if ($existingRates->has($dateString)) {
                $rate = $existingRates->get($dateString);
                
                $this->priceData[] = [
                    'date' => $dateString,
                    'day_name' => $dayName,
                    'base_price' => (string) $rate->base_price,
                    'currency' => $rate->currency,
                    'is_closed' => $rate->is_closed,
                    'min_stay_arrival' => (string) $rate->min_stay_arrival,
                    'status' => $rate->status,
                    'notes' => $rate->notes,
                ];
            } else {
                // Default values for new rate
                $this->priceData[] = [
                    'date' => $dateString,
                    'day_name' => $dayName,
                    'base_price' => '',
                    'currency' => $this->record->hotel->currency ?? 'TRY',
                    'is_closed' => false,
                    'min_stay_arrival' => (string) $this->record->min_stay,
                    'status' => 'available',
                    'notes' => null,
                ];
            }
        }
    }
    
    protected function loadOccupancyRates(Carbon $startDate, Carbon $endDate): void
    {
        // TODO: Implement occupancy-based pricing loading
        // This would be similar to the daily rates, but with occupancy considerations
    }
    
    public function savePrices(): void
    {
        if ($this->priceType === 'daily') {
            $this->saveDailyRates();
        } else {
            $this->saveOccupancyRates();
        }
        
        Notification::make()
            ->success()
            ->title('Başarılı')
            ->body('Fiyatlar kaydedildi.')
            ->send();
    }
    
    protected function saveDailyRates(): void
    {
        foreach ($this->priceData as $data) {
            // Skip if base price is empty
            if (empty($data['base_price'])) {
                continue;
            }
            
            DailyRate::updateOrCreate(
                [
                    'rate_plan_id' => $this->record->id,
                    'date' => $data['date'],
                ],
                [
                    'base_price' => $data['base_price'],
                    'currency' => $data['currency'],
                    'is_closed' => $data['is_closed'],
                    'min_stay_arrival' => $data['min_stay_arrival'],
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                ]
            );
        }
    }
    
    protected function saveOccupancyRates(): void
    {
        // TODO: Implement occupancy-based pricing saving
    }
    
    public function applyBulkPrices(): void
    {
        $data = $this->bulkPriceForm;
        
        if (empty($data['bulk_start_date']) || empty($data['bulk_end_date'])) {
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Lütfen başlangıç ve bitiş tarihlerini girin.')
                ->send();
            return;
        }
        
        $startDate = Carbon::parse($data['bulk_start_date']);
        $endDate = Carbon::parse($data['bulk_end_date']);
        
        // Validate dates
        if ($startDate->isAfter($endDate)) {
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Başlangıç tarihi bitiş tarihinden sonra olamaz.')
                ->send();
            return;
        }
        
        // Apply bulk prices to the selected date range
        foreach ($this->priceData as &$priceItem) {
            $itemDate = Carbon::parse($priceItem['date']);
            
            if ($itemDate->between($startDate, $endDate)) {
                if (isset($data['bulk_price']) && !empty($data['bulk_price'])) {
                    $priceItem['base_price'] = $data['bulk_price'];
                }
                
                if (isset($data['bulk_currency'])) {
                    $priceItem['currency'] = $data['bulk_currency'];
                }
                
                if (isset($data['bulk_min_stay'])) {
                    $priceItem['min_stay_arrival'] = $data['bulk_min_stay'];
                }
                
                if (isset($data['bulk_status'])) {
                    $priceItem['status'] = $data['bulk_status'];
                }
                
                if (isset($data['bulk_is_closed'])) {
                    $priceItem['is_closed'] = $data['bulk_is_closed'];
                }
            }
        }
        
        Notification::make()
            ->success()
            ->title('Başarılı')
            ->body('Toplu fiyat güncellemesi uygulandı.')
            ->send();
    }
    
    public function getBulkPriceFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Toplu Fiyat Güncelleme')
                ->description('Belirli bir tarih aralığı için fiyatları toplu olarak güncelleyin')
                ->schema([
                    Grid::make()
                        ->schema([
                            Card::make()
                                ->schema([
                                    DatePicker::make('bulk_start_date')
                                        ->label('Başlangıç Tarihi')
                                        ->native(false)
                                        ->required(),
                                        
                                    DatePicker::make('bulk_end_date')
                                        ->label('Bitiş Tarihi')
                                        ->native(false)
                                        ->required(),
                                ])
                                ->columns(2),
                                
                            Card::make()
                                ->schema([
                                    TextInput::make('bulk_price')
                                        ->label('Fiyat')
                                        ->numeric()
                                        ->placeholder('100.00'),
                                        
                                    Select::make('bulk_currency')
                                        ->label('Para Birimi')
                                        ->options([
                                            'TRY' => '₺ TL',
                                            'USD' => '$ USD',
                                            'EUR' => '€ EUR',
                                            'GBP' => '£ GBP',
                                        ])
                                        ->default($this->record->hotel->currency ?? 'TRY'),
                                ])
                                ->columns(2),
                                
                            Card::make()
                                ->schema([
                                    TextInput::make('bulk_min_stay')
                                        ->label('Minimum Kalış')
                                        ->numeric()
                                        ->minValue(1)
                                        ->placeholder($this->record->min_stay),
                                        
                                    Select::make('bulk_status')
                                        ->label('Durum')
                                        ->options([
                                            'available' => 'Müsait',
                                            'limited' => 'Sınırlı',
                                            'sold_out' => 'Dolu',
                                        ])
                                        ->default('available'),
                                ])
                                ->columns(2),
                                
                            Toggle::make('bulk_is_closed')
                                ->label('Satışa Kapalı')
                                ->helperText('Satışa kapalı günler için rezervasyon yapılamaz')
                                ->default(false),
                        ]),
                ]),
        ];
    }
}