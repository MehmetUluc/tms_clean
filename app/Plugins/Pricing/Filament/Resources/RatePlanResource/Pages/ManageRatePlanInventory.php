<?php

namespace App\Plugins\Pricing\Filament\Resources\RatePlanResource\Pages;

use App\Plugins\Pricing\Filament\Resources\RatePlanResource;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\Inventory;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\WithFileUploads;

class ManageRatePlanInventory extends Page
{
    use WithFileUploads;
    protected static string $resource = RatePlanResource::class;

    protected static string $view = 'vendor.pricing.rate-plans.manage-inventory';
    
    public ?RatePlan $record = null;
    
    public ?array $inventoryData = [];
    
    public ?array $dateRange = [];
    
    public ?array $bulkInventoryForm = [];
    
    public function mount(RatePlan $record): void
    {
        $this->record = $record;
        
        // Default date range to next 30 days
        $this->dateRange = [
            'start' => Carbon::today()->format('Y-m-d'),
            'end' => Carbon::today()->addDays(30)->format('Y-m-d'),
        ];
        
        $this->loadInventoryData();
    }
    
    public function dateRangeForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\DatePicker::make('dateRange.start')
                            ->label('Başlangıç Tarihi')
                            ->native(false)
                            ->required(),
                            
                        Forms\Components\DatePicker::make('dateRange.end')
                            ->label('Bitiş Tarihi')
                            ->native(false)
                            ->required(),
                        
                        \Filament\Forms\Components\Actions::make([
                            \Filament\Forms\Components\Actions\Action::make('apply')
                                ->label('Uygula')
                                ->color('primary')
                                ->button()
                                ->action(function () {
                                    $this->loadInventoryData();
                                }),
                        ])
                        ->fullWidth(),
                    ])
                    ->columns(1)
            ]);
    }
    
    public function loadInventoryData(): void
    {
        if (empty($this->dateRange['start']) || empty($this->dateRange['end'])) {
            return;
        }
        
        $startDate = Carbon::parse($this->dateRange['start']);
        $endDate = Carbon::parse($this->dateRange['end']);
        
        // Get the room associated with this rate plan
        $room = $this->record->room;
        
        if (!$room) {
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Bu tarife planı bir odaya bağlı değil.')
                ->send();
            return;
        }
        
        // Get existing inventory for the date range
        $existingInventory = Inventory::where('rate_plan_id', $this->record->id)
            ->where('room_id', $room->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function ($inventory) {
                return $inventory->date->format('Y-m-d');
            });
            
        // Create data array for form
        $this->inventoryData = [];
        
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
            
            // Check if we have existing inventory for this date
            if ($existingInventory->has($dateString)) {
                $inventory = $existingInventory->get($dateString);
                
                $this->inventoryData[] = [
                    'date' => $dateString,
                    'day_name' => $dayName,
                    'available' => (string) $inventory->available,
                    'total' => (string) $inventory->total,
                    'is_closed' => $inventory->is_closed,
                    'stop_sell' => $inventory->stop_sell,
                    'notes' => $inventory->notes,
                ];
            } else {
                // Default values for new inventory
                $this->inventoryData[] = [
                    'date' => $dateString,
                    'day_name' => $dayName,
                    'available' => (string) ($room->capacity_adults ?? 1),
                    'total' => (string) ($room->capacity_adults ?? 1),
                    'is_closed' => false,
                    'stop_sell' => false,
                    'notes' => null,
                ];
            }
        }
    }
    
    public function saveInventory(): void
    {
        // Get the room associated with this rate plan
        $room = $this->record->room;
        
        if (!$room) {
            Notification::make()
                ->danger()
                ->title('Hata')
                ->body('Bu tarife planı bir odaya bağlı değil.')
                ->send();
            return;
        }
        
        foreach ($this->inventoryData as $data) {
            // Skip if available count is empty
            if (empty($data['available'])) {
                continue;
            }
            
            Inventory::updateOrCreate(
                [
                    'rate_plan_id' => $this->record->id,
                    'room_id' => $room->id,
                    'date' => $data['date'],
                ],
                [
                    'available' => $data['available'],
                    'total' => $data['total'],
                    'is_closed' => $data['is_closed'],
                    'stop_sell' => $data['stop_sell'],
                    'notes' => $data['notes'],
                ]
            );
        }
        
        Notification::make()
            ->success()
            ->title('Başarılı')
            ->body('Kontenjan bilgileri kaydedildi.')
            ->send();
    }
    
    public function applyBulkInventory(): void
    {
        $data = $this->bulkInventoryForm;
        
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
        
        // Apply bulk inventory to the selected date range
        foreach ($this->inventoryData as &$inventoryItem) {
            $itemDate = Carbon::parse($inventoryItem['date']);
            
            if ($itemDate->between($startDate, $endDate)) {
                if (isset($data['bulk_available']) && !empty($data['bulk_available'])) {
                    $inventoryItem['available'] = $data['bulk_available'];
                }
                
                if (isset($data['bulk_total']) && !empty($data['bulk_total'])) {
                    $inventoryItem['total'] = $data['bulk_total'];
                }
                
                if (isset($data['bulk_is_closed'])) {
                    $inventoryItem['is_closed'] = $data['bulk_is_closed'];
                }
                
                if (isset($data['bulk_stop_sell'])) {
                    $inventoryItem['stop_sell'] = $data['bulk_stop_sell'];
                }
            }
        }
        
        Notification::make()
            ->success()
            ->title('Başarılı')
            ->body('Toplu kontenjan güncellemesi uygulandı.')
            ->send();
    }
    
    public function getBulkInventoryFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Toplu Kontenjan Güncelleme')
                ->description('Belirli bir tarih aralığı için kontenjanları toplu olarak güncelleyin')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\DatePicker::make('bulk_start_date')
                                        ->label('Başlangıç Tarihi')
                                        ->native(false)
                                        ->required(),
                                        
                                    Forms\Components\DatePicker::make('bulk_end_date')
                                        ->label('Bitiş Tarihi')
                                        ->native(false)
                                        ->required(),
                                ])
                                ->columns(2),
                                
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\TextInput::make('bulk_available')
                                        ->label('Müsait')
                                        ->numeric()
                                        ->minValue(0)
                                        ->placeholder('Müsait oda sayısı'),
                                        
                                    Forms\Components\TextInput::make('bulk_total')
                                        ->label('Toplam')
                                        ->numeric()
                                        ->minValue(0)
                                        ->placeholder('Toplam kapasite'),
                                ])
                                ->columns(2),
                                
                            Forms\Components\Card::make()
                                ->schema([
                                    Forms\Components\Toggle::make('bulk_is_closed')
                                        ->label('Satışa Kapalı')
                                        ->helperText('Satışa kapalı günler için rezervasyon yapılamaz')
                                        ->default(false),
                                        
                                    Forms\Components\Toggle::make('bulk_stop_sell')
                                        ->label('Geçici Durdur')
                                        ->helperText('Geçici olarak satışı durdur')
                                        ->default(false),
                                ])
                                ->columns(2),
                        ]),
                ]),
        ];
    }
}