<?php

namespace App\Plugins\Pricing\Filament\Pages;

use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Services\PricingService;
use Carbon\Carbon;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;

class HotelPricingPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.hotel-pricing-page';

    protected static ?string $navigationLabel = 'Otel Fiyat Yönetimi';

    protected static ?string $title = 'Otel Fiyat Yönetimi';

    protected static ?int $navigationSort = 3;

    /**
     * The hotel property - can be either a Hotel model or an ID string when coming from form
     * @var \App\Plugins\Accommodation\Models\Hotel|string|null
     */
    public $hotel = null;
    
    /**
     * The selected hotel ID from the form
     * @var int|string|null
     */
    public $selectedHotelId = null;
    
    /**
     * Define Livewire property attributes
     */
    // Livewire form için kullanılan özellikler
    public $formSelectedHotelId;
    public $formSelectedRooms = [];
    public $formSelectedBoardTypes = [];
    public $formStartDate;
    public $formEndDate;
    public $formSelectedDays = [];
    public $formRefundType = 'refundable'; // Varsayılan olarak iade edilebilir
    
    public ?array $selectedRooms = [];

    public ?array $selectedBoardTypes = [];

    public ?array $selectedDays = [];

    public ?string $startDate = null;

    public ?string $endDate = null;

    public ?string $selectedRefundType = 'refundable';

    public bool $showPricingForm = false;

    public bool $isLoading = false;

    public array $roomsData = [];

    public array $boardTypesData = [];

    public array $ratePlansData = [];

    public array $pricingData = [];

    /**
     * Hotel specific data including refund policy
     * @var array
     */
    public array $hotelData = [];

    private PricingService $pricingService;

    public function boot(): void
    {
        $this->pricingService = app(PricingService::class);
    }

    public function mount($hotel = null, $hotel_id = null): void
    {
        // Doğrudan URL parametrelerini de kontrol et
        if ($hotel_id === null) {
            $hotel_id = request()->query('hotel_id');
        }

        // Mount method called

        // Set default dates (today to +7 days)
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->addDays(7)->format('Y-m-d');

        // Set default days (all days)
        $this->selectedDays = ['1', '2', '3', '4', '5', '6', '7'];

        // Initialize form data
        $this->formStartDate = Carbon::today()->format('Y-m-d');
        $this->formEndDate = Carbon::today()->addDays(7)->format('Y-m-d');
        $this->formSelectedDays = ['1', '2', '3', '4', '5', '6', '7'];

        // Determine hotel ID - check hotel_id param first, then fallback to hotel param
        $hotelIdToLoad = null;

        if ($hotel_id) {
            $hotelIdToLoad = $hotel_id;
            // Using hotel_id parameter
        } elseif ($hotel) {
            $hotelIdToLoad = is_numeric($hotel) ? intval($hotel) : $hotel;
            // Using hotel parameter
        }

        // If a hotel ID was provided via URL
        if ($hotelIdToLoad) {
            try {
                $hotelModel = Hotel::find($hotelIdToLoad);

                // Loading hotel from parameter

                if ($hotelModel) {
                    // Set properties
                    $this->selectedHotelId = $hotelIdToLoad;
                    $this->formSelectedHotelId = $hotelIdToLoad;
                    $this->hotel = $hotelModel;
                    $this->loadRoomsAndBoardTypes();
                    
                    // Hotel set from parameter
                }
            } catch (\Exception $e) {
                Log::error('Error mounting with hotel parameter: ' . $e->getMessage());
                $this->hotel = null;
                $this->selectedHotelId = null;
                $this->formSelectedHotelId = null;
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Fiyat Görüntüleme Kriterleri')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('formSelectedHotelId')
                                    ->label('Otel')
                                    ->options(function() {
                                        // Eğer seçili bir otel varsa, o oteli de options listesine ekleyelim
                                        $options = [];

                                        // Seçili oteli ekleyelim (eğer varsa)
                                        if ($this->selectedHotelId) {
                                            $selectedHotel = Hotel::find($this->selectedHotelId);
                                            if ($selectedHotel) {
                                                $options[$selectedHotel->id] = $selectedHotel->name;
                                                // Added selected hotel to options list
                                            }
                                        }

                                        // Diğer otelleri de ekleyelim
                                        $otherHotels = Hotel::when($this->selectedHotelId, function($query) {
                                                return $query->where('id', '!=', $this->selectedHotelId);
                                            })
                                            ->orderBy('name')
                                            ->limit(99) // Seçili otel + 99 diğer otel = maksimum 100 otel
                                            ->pluck('name', 'id')
                                            ->toArray();

                                        return $options + $otherHotels;
                                    })
                                    ->getSearchResultsUsing(function (string $search) {
                                        // Arama fonksiyonu: Sadece otel adına göre ara
                                        return Hotel::where('name', 'like', "%{$search}%")
                                            ->limit(50) // Maksimum 50 sonuç göster
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->searchable() // Arama yapılabilsin
                                    ->searchPrompt('Otel adı giriniz...')
                                    ->searchDebounce(500) // Performans için arama gecikmesi ekle (ms)
                                    ->required()
                                    ->preload() // Sayfa yüklendiğinde sonuçları önceden yükle
                                    ->placeholder('Otel adını arayarak seçiniz...')
                                    ->default(fn() => $this->formSelectedHotelId) // Varsayılan değer olarak seçili hotel ID kullanılsın
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        // Hide pricing form when hotel changes
                                        $this->showPricingForm = false;
                                        // Hotel state updated in select box
                                        
                                        // Reset related data
                                        $this->selectedRooms = [];
                                        $this->formSelectedRooms = [];
                                        $this->selectedBoardTypes = [];
                                        $this->formSelectedBoardTypes = [];
                                        $this->roomsData = [];
                                        $this->boardTypesData = [];
                                        
                                        // If no selection, reset everything
                                        if (!$state) {
                                            $this->hotel = null;
                                            $this->selectedHotelId = null;
                                            $this->formSelectedHotelId = null;
                                            // Hotel selection cleared
                                            return;
                                        }
                                        
                                        // Always treat state as an ID and find the hotel model
                                        try {
                                            $hotelId = is_numeric($state) ? intval($state) : $state;
                                            // Loading hotel model
                                            $hotel = Hotel::find($hotelId);
                                            
                                            if ($hotel) {
                                                // Hotel model found
                                                
                                                // Important: Set both properties
                                                $this->selectedHotelId = $hotelId;
                                                $this->formSelectedHotelId = $hotelId;
                                                $this->hotel = $hotel;
                                                
                                                // Load dependent data
                                                $this->loadRoomsAndBoardTypes();
                                            } else {
                                                Log::warning('Hotel not found with ID: ' . $hotelId);
                                                $this->hotel = null;
                                                $this->selectedHotelId = null;
                                                $this->formSelectedHotelId = null;
                                            }
                                        } catch (\Exception $e) {
                                            Log::error('Error updating hotel state: ' . $e->getMessage());
                                            $this->hotel = null;
                                            $this->selectedHotelId = null;
                                            $this->formSelectedHotelId = null;
                                        }
                                    }),
                                
                                CheckboxList::make('formSelectedRooms')
                                    ->label('Odalar')
                                    ->options(function (callable $get) {
                                        $hotelId = $get('formSelectedHotelId');
                                        if (!$hotelId) {
                                            return [];
                                        }
                                        
                                        $options = Room::where('hotel_id', $hotelId)
                                                 ->pluck('name', 'id')
                                                 ->toArray();
                                        
                                        // Room options loaded
                                        
                                        return $options;
                                    })
                                    ->columns(2)
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->selectedRooms = $state ?? [];
                                        $this->formSelectedRooms = $state ?? [];
                                        // Hide form when selection changes
                                        $this->showPricingForm = false;
                                    }),
                                
                                CheckboxList::make('formSelectedBoardTypes')
                                    ->label('Pansiyon Tipleri')
                                    ->options(function (callable $get) {
                                        $hotelId = $get('formSelectedHotelId');
                                        if (!$hotelId) {
                                            return []; // Hotel seçilmeden önce boş döndür
                                        }

                                        try {
                                            $hotel = Hotel::find($hotelId);
                                            if (!$hotel) {
                                                \Log::error('Pansiyon tipleri için hotel bulunamadı', ['hotel_id' => $hotelId]);
                                                return BoardType::pluck('name', 'id')->toArray();
                                            }

                                            // Hotel'in pansiyon tiplerini dinamik olarak yükle
                                            // Önce "hotel_board_types" tablosunu kontrol et (eğer varsa)
                                            if (Schema::hasTable('hotel_board_types')) {
                                                try {
                                                    $hotelBoardTypeIds = \DB::table('hotel_board_types')
                                                        ->where('hotel_id', $hotelId)
                                                        ->pluck('board_type_id')
                                                        ->toArray();

                                                    if (!empty($hotelBoardTypeIds)) {
                                                        $boardTypes = BoardType::whereIn('id', $hotelBoardTypeIds)
                                                            ->pluck('name', 'id')
                                                            ->toArray();

                                                        // Hotel için özel pansiyon tipleri yüklendi (Hotel-specific board types loaded)

                                                        return $boardTypes;
                                                    }
                                                } catch (\Exception $e) {
                                                    Log::warning('Error loading hotel-specific board types, showing all board types: ' . $e->getMessage());
                                                }
                                            }

                                            // İlişki üzerinden pansiyon tiplerini yüklemeyi dene
                                            if (method_exists($hotel, 'boardTypes')) {
                                                try {
                                                    $boardTypes = $hotel->boardTypes()
                                                        ->pluck('name', 'id')
                                                        ->toArray();

                                                    if (!empty($boardTypes)) {
                                                        // Board types loaded from hotel relationship

                                                        return $boardTypes;
                                                    }
                                                } catch (\Exception $e) {
                                                    Log::warning('Error loading board types from hotel relationship: ' . $e->getMessage());
                                                }
                                            }

                                            // Fallback: Tüm pansiyon tiplerini göster
                                            // No hotel-specific board types found, showing all board types
                                            return BoardType::pluck('name', 'id')->toArray();

                                        } catch (\Exception $e) {
                                            Log::error('Error loading board types: ' . $e->getMessage());
                                            return BoardType::pluck('name', 'id')->toArray();
                                        }
                                    })
                                    ->columns(2)
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->selectedBoardTypes = $state ?? [];
                                        $this->formSelectedBoardTypes = $state ?? [];
                                        // Hide form when selection changes
                                        $this->showPricingForm = false;
                                    }),
                            ]),
                            
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('formStartDate')
                                    ->label('Başlangıç Tarihi')
                                    ->required()
                                    ->native(false) // Native olmayan (JavaScript) datepicker kullanılsın
                                    ->displayFormat('d.m.Y') // Türkçe tarih formatı gösterimi
                                    ->format('Y-m-d') // Veritabanı için format
                                    ->closeOnDateSelection() // Tarih seçildiğinde picker kapansın
                                    ->minDate(Carbon::today())
                                    ->default(Carbon::today())
                                    ->live() // Canlı güncelleme
                                    ->afterStateUpdated(function ($state) {
                                        $this->startDate = $state;

                                        // Automatically adjust endDate if startDate is after endDate
                                        if ($this->endDate && $state && Carbon::parse($state)->isAfter(Carbon::parse($this->endDate))) {
                                            $this->formEndDate = Carbon::parse($state)->addDays(7)->format('Y-m-d');
                                            $this->endDate = $this->formEndDate;
                                        }

                                        // Hide form when date changes
                                        $this->showPricingForm = false;
                                    }),

                                DatePicker::make('formEndDate')
                                    ->label('Bitiş Tarihi')
                                    ->required()
                                    ->native(false) // Native olmayan (JavaScript) datepicker kullanılsın
                                    ->displayFormat('d.m.Y') // Türkçe tarih formatı gösterimi
                                    ->format('Y-m-d') // Veritabanı için format
                                    ->closeOnDateSelection() // Tarih seçildiğinde picker kapansın
                                    ->minDate(function (callable $get) {
                                        $startDate = $get('formStartDate');
                                        return $startDate ? Carbon::parse($startDate) : Carbon::today();
                                    })
                                    ->default(Carbon::today()->addDays(7))
                                    ->live() // Canlı güncelleme
                                    ->afterStateUpdated(function ($state) {
                                        $this->endDate = $state;
                                        // Hide form when date changes
                                        $this->showPricingForm = false;
                                    }),
                            ]),

                        // İade Politikası Seçimi
                        Grid::make(1)
                            ->schema([
                                \Filament\Forms\Components\Radio::make('formRefundType')
                                    ->label('İade Politikası')
                                    ->options(function (callable $get) {
                                        $hotelId = $get('formSelectedHotelId');

                                        if (!$hotelId) {
                                            return []; // Hotel seçilmeden önce boş seçenek göster
                                        }

                                        try {
                                            $hotel = Hotel::find($hotelId);
                                            $options = [];

                                            if ($hotel) {
                                                // Veritabanından hotelin izin verdiği iade politikalarını al
                                                $allowRefundable = $hotel->allow_refundable ?? true;
                                                $allowNonRefundable = $hotel->allow_non_refundable ?? true;
                                                $nonRefundableDiscount = $hotel->non_refundable_discount ?? 0;

                                                // Hotel izin verirse İade Edilebilir seçeneğini göster
                                                if ($allowRefundable) {
                                                    $options['refundable'] = 'İade Edilebilir';
                                                }

                                                // Hotel izin verirse İade Edilemez seçeneğini göster
                                                if ($allowNonRefundable) {
                                                    $label = 'İade Edilemez';
                                                    if ($nonRefundableDiscount > 0) {
                                                        $label .= ' (%' . $nonRefundableDiscount . ' indirimli)';
                                                    }
                                                    $options['non_refundable'] = $label;
                                                }

                                                // Refund policy options loaded for hotel

                                                // En az bir seçenek olmalı
                                                if (empty($options)) {
                                                    $options['refundable'] = 'İade Edilebilir';
                                                    // No refund policy found for hotel, added default refundable option
                                                }
                                            } else {
                                                Log::error('Hotel not found with ID: ' . $hotelId);
                                                return [
                                                    'refundable' => 'İade Edilebilir',
                                                ];
                                            }

                                            return $options;
                                        } catch (\Exception $e) {
                                            Log::error('Could not get hotel refund policy: ' . $e->getMessage());
                                            return [
                                                'refundable' => 'İade Edilebilir',
                                            ];
                                        }
                                    })
                                    ->default(function (callable $get) {
                                        // Hotel seçildiğinde varsayılan değeri belirle
                                        $hotelId = $get('formSelectedHotelId');
                                        if (!$hotelId) {
                                            return 'refundable';
                                        }

                                        $hotel = Hotel::find($hotelId);
                                        if (!$hotel) {
                                            return 'refundable';
                                        }

                                        // Hotel izin veriyorsa refundable, vermiyorsa non_refundable
                                        $allowRefundable = $hotel->allow_refundable ?? true;
                                        $allowNonRefundable = $hotel->allow_non_refundable ?? true;

                                        if ($allowRefundable) {
                                            return 'refundable';
                                        } else if ($allowNonRefundable) {
                                            return 'non_refundable';
                                        }

                                        return 'refundable';
                                    })
                                    ->inline()
                                    ->columnSpanFull()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->selectedRefundType = $state;
                                        $this->formRefundType = $state;
                                        // Hide form when refund type changes
                                        $this->showPricingForm = false;

                                        // Refund type selection changed
                                    }),
                            ]),
                            
                        CheckboxList::make('formSelectedDays')
                            ->label('Günler')
                            ->options([
                                '1' => 'Pazartesi',
                                '2' => 'Salı',
                                '3' => 'Çarşamba',
                                '4' => 'Perşembe',
                                '5' => 'Cuma',
                                '6' => 'Cumartesi',
                                '7' => 'Pazar',
                            ])
                            ->columns(7)
                            ->default(['1', '2', '3', '4', '5', '6', '7'])
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedDays = $state ?? [];
                                $this->formSelectedDays = $state ?? [];
                                // Hide form when days change
                                $this->showPricingForm = false;
                            }),
                    ]),
            ]);
    }

    /**
     * Load rooms and board types for the selected hotel
     *
     * @return void
     */
    private function loadRoomsAndBoardTypes(): void
    {
        try {
            // Reset data if hotel is not valid
            if (!$this->hotel) {
                $this->resetRoomAndBoardTypeData();
                return;
            }

            // If hotel is a string (ID), try to find the model
            if (is_string($this->hotel) && is_numeric($this->hotel)) {
                $hotelModel = Hotel::find((int)$this->hotel);
                if ($hotelModel) {
                    $this->hotel = $hotelModel;
                } else {
                    $this->resetRoomAndBoardTypeData();
                    return;
                }
            }

            // Check if hotel is a proper object with an id property
            if (!is_object($this->hotel) || !property_exists($this->hotel, 'id') || !$this->hotel->id) {
                $this->resetRoomAndBoardTypeData();
                return;
            }

            $hotelId = $this->hotel->id;

            // Get rooms for this hotel - include pricing_calculation_method field
            $rooms = Room::where('hotel_id', $hotelId)->get();

            // Rooms loaded for hotel

            // Load detailed room data with pricing_calculation_method
            $this->roomsData = $rooms->keyBy('id')->map(function ($room) {
                return array_merge($room->toArray(), [
                    'pricing_calculation_method' => $room->pricing_calculation_method ?? 'per_person',
                    'pricing_calculation_method_label' => $room->pricing_calculation_method_label ?? 'Kişi Başı Fiyatlandırma',
                    'is_per_person' => $room->pricing_calculation_method === 'per_person',
                    'capacity' => $room->capacity_adults ?? 2,
                ]);
            })->toArray();

            // Get board types only for this hotel
            // First check if we have hotel_board_types table (PricingV2)
            if (Schema::hasTable('hotel_board_types')) {
                try {
                    // Get board types through the hotel-board type relationship
                    $hotelBoardTypes = DB::table('hotel_board_types')
                        ->where('hotel_id', $hotelId)
                        ->pluck('board_type_id')
                        ->toArray();

                    $boardTypes = BoardType::whereIn('id', $hotelBoardTypes)->get();

                    \Log::debug('Board types loaded from hotel-board-types relationship', [
                        'hotel_id' => $hotelId,
                        'board_type_count' => $boardTypes->count(),
                        'board_type_ids' => $boardTypes->pluck('id')->toArray()
                    ]);
                } catch (\Exception $e) {
                    // Fallback to all board types if something goes wrong
                    \Log::warning('Error loading hotel-specific board types, falling back to all board types', [
                        'hotel_id' => $hotelId,
                        'error' => $e->getMessage()
                    ]);
                    $boardTypes = BoardType::all();
                }
            } else {
                // Check if hotel has boardTypes relationship directly
                if (method_exists($this->hotel, 'boardTypes')) {
                    try {
                        $boardTypes = $this->hotel->boardTypes;

                        \Log::debug('Board types loaded from hotel relationship', [
                            'hotel_id' => $hotelId,
                            'board_type_count' => $boardTypes->count(),
                            'board_type_ids' => $boardTypes->pluck('id')->toArray()
                        ]);
                    } catch (\Exception $e) {
                        // Fallback to all board types if something goes wrong
                        \Log::warning('Error loading hotel board types from relationship, falling back to all', [
                            'hotel_id' => $hotelId,
                            'error' => $e->getMessage()
                        ]);
                        $boardTypes = BoardType::all();
                    }
                } else {
                    // Fallback to all board types if no relationship exists
                    $boardTypes = BoardType::all();
                    \Log::info('No hotel-board types relationship found, using all board types', [
                        'hotel_id' => $hotelId,
                        'board_type_count' => $boardTypes->count()
                    ]);
                }
            }

            // Get refund policy information from hotel
            $refundPolicy = [
                'allow_refundable' => $this->hotel->allow_refundable ?? true,
                'allow_non_refundable' => $this->hotel->allow_non_refundable ?? true,
                'non_refundable_discount' => $this->hotel->non_refundable_discount ?? 0,
                'refund_policy' => $this->hotel->refund_policy ?? '',
            ];

            // Hotel refund policy loaded

            // Store board types with more details
            $this->boardTypesData = $boardTypes->keyBy('id')->toArray();

            // Also store hotel refund policy data for use in pricing form
            $this->hotelData = [
                'id' => $this->hotel->id,
                'name' => $this->hotel->name,
                'refund_policy' => $refundPolicy
            ];
        } catch (\Exception $e) {
            Log::error('Error loading rooms and board types: ' . $e->getMessage());
            $this->resetRoomAndBoardTypeData();
        }
    }

    /**
     * Reset room and board type data
     * 
     * @return void
     */
    private function resetRoomAndBoardTypeData(): void
    {
        $this->roomsData = [];
        $this->boardTypesData = [];
    }

    /**
     * Check and create rate plans for selected rooms and board types
     * 
     * @return void
     */
    private function checkRatePlans(): void
    {
        try {
            $this->showPricingForm = false;
            $this->ratePlansData = [];
            
            // Validate required data
            if (empty($this->selectedRooms) || empty($this->selectedBoardTypes)) {
                return;
            }
            
            // Get hotel ID safely
            $hotelId = $this->getHotelId();
            if (!$hotelId) {
                return;
            }

            $ratePlans = collect();
            
            // For each room-board type combination, check if rate plan exists or create it
            foreach ($this->selectedRooms as $roomId) {
                foreach ($this->selectedBoardTypes as $boardTypeId) {
                    try {
                        // Log detailed information to diagnose the issue
                        \Log::info("Starting rate plan creation process", [
                            'hotel_id' => $hotelId,
                            'room_id' => $roomId,
                            'board_type_id' => $boardTypeId
                        ]);

                        // Check if the room exists
                        $room = Room::find($roomId);
                        if (!$room) {
                            \Log::error("Room not found", ['room_id' => $roomId]);
                            $this->addError('form', "Oda bulunamadı (ID: {$roomId})");
                            continue;
                        }

                        // Check if the board type exists
                        $boardType = BoardType::find($boardTypeId);
                        if (!$boardType) {
                            \Log::error("Board type not found", ['board_type_id' => $boardTypeId]);
                            $this->addError('form', "Pansiyon tipi bulunamadı (ID: {$boardTypeId})");
                            continue;
                        }

                        \Log::info("Found related entities", [
                            'room' => $room->name,
                            'board_type' => $boardType->name,
                        ]);

                        $isPerPerson = $room->is_per_person ?? true;

                        try {
                            // Use the pricing service to create or get the rate plan
                            $ratePlan = $this->pricingService->getRatePlanRepository()->createOrUpdate(
                                $hotelId,
                                $roomId,
                                $boardTypeId,
                                $isPerPerson
                            );

                            \Log::info("Rate plan managed successfully", ['id' => $ratePlan->id]);

                            // Add attributes for the template
                            $ratePlan->setAttribute('room_name', $room->name);
                            $ratePlan->setAttribute('board_type_name', $boardType->name);

                            // Add pricing calculation method information
                            $ratePlan->setAttribute('pricing_calculation_method', $room->pricing_calculation_method ?? 'per_person');
                            $ratePlan->setAttribute('is_per_person', ($room->pricing_calculation_method ?? 'per_person') === 'per_person');
                            $ratePlan->setAttribute('capacity', $room->capacity_adults ?? 2);

                            // Get refund options for this room
                            $refundOptions = $this->determineRefundOptions($roomId);

                            // Add refund information - use the selected refund type from the form
                            $ratePlan->setAttribute('allow_refundable', $refundOptions['allow_refundable']);
                            $ratePlan->setAttribute('allow_non_refundable', $refundOptions['allow_non_refundable']);
                            $ratePlan->setAttribute('non_refundable_discount', $refundOptions['non_refundable_discount']);

                            // Use formda seçilen iade tipi öncelikli, sonra refund options'a bakılır
                            if (!empty($this->selectedRefundType) &&
                                (($this->selectedRefundType === 'refundable' && $refundOptions['allow_refundable']) ||
                                 ($this->selectedRefundType === 'non_refundable' && $refundOptions['allow_non_refundable']))) {
                                $ratePlan->setAttribute('selected_refund_type', $this->selectedRefundType);
                            } else {
                                $ratePlan->setAttribute('selected_refund_type', $refundOptions['selected_type']);
                            }

                            // Add display info for refund policy
                            if ($refundOptions['allow_refundable'] && $refundOptions['allow_non_refundable']) {
                                $ratePlan->setAttribute('refund_policy_label', 'İade Edilebilir / İade Edilemez Seçenekli');
                            } else if ($refundOptions['allow_refundable']) {
                                $ratePlan->setAttribute('refund_policy_label', 'Sadece İade Edilebilir');
                            } else if ($refundOptions['allow_non_refundable']) {
                                $ratePlan->setAttribute('refund_policy_label', 'Sadece İade Edilemez');
                            } else {
                                $ratePlan->setAttribute('refund_policy_label', 'Belirtilmemiş');
                            }

                            $ratePlans->push($ratePlan);
                        } catch (\Exception $e) {
                            \Log::error("Database error creating rate plan: " . $e->getMessage(), [
                                'hotel_id' => $hotelId,
                                'room_id' => $roomId,
                                'board_type_id' => $boardTypeId,
                                'exception' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);

                            $this->addError('form', "Fiyat planı oluşturulurken bir hata oluştu: " . $e->getMessage());
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error creating rate plan for room {$roomId} and board type {$boardTypeId}: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString()
                        ]);
                        $this->addError('form', "Oda ve pansiyon tipi kombinasyonu için fiyat planı oluşturulamadı: " . $e->getMessage());
                    }
                }
            }
            
            $this->ratePlansData = $ratePlans->keyBy('id')->toArray();
        } catch (\Exception $e) {
            \Log::error('Error checking rate plans: ' . $e->getMessage());
            $this->ratePlansData = [];
        }
    }
    
    /**
     * Get the hotel ID safely
     * 
     * @return int|null
     */
    private function getHotelId(): ?int
    {
        \Log::info('getHotelId called', [
            'selectedHotelId' => $this->selectedHotelId,
            'hotel_type' => gettype($this->hotel),
            'hotel_object_id' => is_object($this->hotel) && property_exists($this->hotel, 'id') ? $this->hotel->id : null
        ]);
        
        // First priority: Check selectedHotelId
        if ($this->selectedHotelId) {
            if (is_numeric($this->selectedHotelId)) {
                return (int)$this->selectedHotelId;
            }
            
            if (is_string($this->selectedHotelId) && is_numeric($this->selectedHotelId)) {
                return (int)$this->selectedHotelId;
            }
        }
        
        // Second priority: Check hotel object
        if (is_object($this->hotel) && property_exists($this->hotel, 'id') && $this->hotel->id) {
            return (int)$this->hotel->id;
        }
        
        // Third priority: Check if hotel is an ID directly
        if (is_numeric($this->hotel)) {
            return (int)$this->hotel;
        }
        
        if (is_string($this->hotel) && is_numeric($this->hotel)) {
            return (int)$this->hotel;
        }
        
        // Nothing found
        \Log::warning('No hotel ID found', [
            'selectedHotelId' => $this->selectedHotelId,
            'hotel' => $this->hotel
        ]);
        
        return null;
    }

    /**
     * Generate pricing form based on selected criteria
     *
     * @return void
     */
    public function generatePricingForm(): void
    {
        try {
            // Start loading spinner, but also show form immediately
            $this->isLoading = true;
            $this->showPricingForm = true;

            // First, sync form data to our properties
            $this->selectedHotelId = $this->formSelectedHotelId;
            $this->selectedRooms = $this->formSelectedRooms;
            $this->selectedBoardTypes = $this->formSelectedBoardTypes;

            // Using date fields directly
            $this->startDate = $this->formStartDate ?? Carbon::today()->format('Y-m-d');
            $this->endDate = $this->formEndDate ?? Carbon::today()->addDays(7)->format('Y-m-d');

            \Log::debug('Using date fields directly', [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate
            ]);

            $this->selectedDays = $this->formSelectedDays;
            $this->selectedRefundType = $this->formRefundType;

            // Log initial state to detect potential issues
            \Log::info('Generate Pricing Form started', [
                'selectedHotelId' => $this->selectedHotelId,
                'selectedRooms' => $this->selectedRooms,
                'selectedBoardTypes' => $this->selectedBoardTypes,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'selectedDays' => $this->selectedDays,
                'selectedRefundType' => $this->selectedRefundType,
            ]);
            
            // Validate form manually to ensure we have the right values
            $this->validate([
                'formSelectedHotelId' => 'required',
                'formSelectedRooms' => 'required|array|min:1',
                'formSelectedBoardTypes' => 'required|array|min:1',
                'formStartDate' => 'required|date',
                'formEndDate' => 'required|date|after_or_equal:formStartDate',
                'formSelectedDays' => 'required|array|min:1',
                'formRefundType' => 'required|in:refundable,non_refundable',
            ], [
                'formSelectedHotelId.required' => 'Lütfen bir otel seçin.',
                'formSelectedRooms.required' => 'Lütfen en az bir oda seçin.',
                'formSelectedBoardTypes.required' => 'Lütfen en az bir pansiyon tipi seçin.',
                'formStartDate.required' => 'Lütfen başlangıç tarihi seçin.',
                'formEndDate.required' => 'Lütfen bitiş tarihi seçin.',
                'formEndDate.after_or_equal' => 'Bitiş tarihi başlangıç tarihinden önce olamaz.',
                'formSelectedDays.required' => 'Lütfen en az bir gün seçin.',
                'formRefundType.required' => 'Lütfen iade politikası seçin.',
                'formRefundType.in' => 'Geçersiz iade politikası.',
            ]);
            
            // Additional check to log values even after validation
            \Log::info('Form values after validation', [
                'selectedHotelId_type' => gettype($this->selectedHotelId),
                'selectedHotelId_value' => $this->selectedHotelId,
                'selectedRooms' => $this->selectedRooms,
                'selectedBoardTypes' => $this->selectedBoardTypes,
            ]);
            
            // Make sure we have a valid hotel
            if (is_null($this->hotel) || (is_object($this->hotel) && !property_exists($this->hotel, 'id'))) {
                // Try to load the hotel from the selected ID
                try {
                    $hotelId = is_numeric($this->selectedHotelId) ? intval($this->selectedHotelId) : $this->selectedHotelId;
                    $hotelModel = Hotel::find($hotelId);

                    if ($hotelModel) {
                        $this->hotel = $hotelModel;
                    } else {
                        $this->addError('form', 'Seçilen otel bulunamadı.');
                        return;
                    }
                } catch (\Exception $e) {
                    \Log::error('Error loading hotel: ' . $e->getMessage());
                    $this->addError('form', 'Otel verisi yüklenirken bir hata oluştu.');
                    return;
                }
            }

            // Make sure rooms and board types are loaded for this hotel
            \Log::info('Loading rooms and board types for hotel', [
                'hotel_id' => $this->selectedHotelId,
                'hotel_object' => is_object($this->hotel) ? get_class($this->hotel) : gettype($this->hotel)
            ]);
            $this->loadRoomsAndBoardTypes();
            
            // Get hotel ID safely
            $hotelId = $this->getHotelId();
            if (!$hotelId) {
                $this->addError('form', 'Geçerli bir otel ID bulunamadı.');
                return;
            }
            
            // Validate date inputs
            $this->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
                'selectedDays' => 'required|array|min:1',
            ]);
            
            $startDate = Carbon::parse($this->startDate);
            $endDate = Carbon::parse($this->endDate);
            
            // Generate date array based on selected days
            $dateRange = [];
            $current = $startDate->copy();
            
            while ($current->lte($endDate)) {
                $dayOfWeek = $current->dayOfWeekIso;
                
                if (in_array((string)$dayOfWeek, $this->selectedDays)) {
                    $dateRange[] = $current->format('Y-m-d');
                }
                
                $current->addDay();
            }
            
            if (empty($dateRange)) {
                $this->addError('selectedDays', 'Seçilen tarih aralığında uygun gün bulunamadı.');
                return;
            }
            
            // Make sure we have rate plans
            if (empty($this->ratePlansData)) {
                \Log::info('No rate plans found, running checkRatePlans');
                $this->checkRatePlans();
                
                if (empty($this->ratePlansData)) {
                    $this->addError('form', 'Seçilen oda ve pansiyon tipi kombinasyonları için fiyat planı oluşturulamadı.');
                    return;
                }
            }
            
            // Get pricing data for each rate plan
            $pricingData = [];
            
            foreach ($this->ratePlansData as $ratePlanId => $ratePlan) {
                try {
                    $pricingByDate = $this->pricingService->getPricingDataForDateRange(
                        $ratePlanId,
                        $startDate,
                        $endDate
                    );
                    
                    // Filter by selected days
                    $filteredPricing = array_filter($pricingByDate, function ($key) use ($dateRange) {
                        return in_array($key, $dateRange);
                    }, ARRAY_FILTER_USE_KEY);
                    
                    $pricingData[$ratePlanId] = $filteredPricing;
                } catch (\Exception $e) {
                    \Log::error("Error getting pricing data for rate plan {$ratePlanId}: " . $e->getMessage());
                }
            }
            
            $this->pricingData = $pricingData;
            $this->showPricingForm = true;
            
            // Log successful state
            \Log::info('Generate Pricing Form success', [
                'ratePlansCount' => count($this->ratePlansData),
                'dateRangeCount' => count($dateRange),
                'showPricingForm' => $this->showPricingForm
            ]);
            
            // Ensure form is marked as ready to show first
            $this->showPricingForm = true;

            // Check if rooms data is empty - reload if necessary
            if (empty($this->roomsData) && $this->hotel) {
                \Log::warning('Room data is empty, reloading rooms and board types before dispatch');
                $this->loadRoomsAndBoardTypes();
            }

            // Log what we're sending to the child component
            \Log::info('About to dispatch pricingDataReady event', [
                'ratePlansCount' => count($this->ratePlansData),
                'roomsDataCount' => count($this->roomsData),
                'boardTypesDataCount' => count($this->boardTypesData),
                'dateRangeCount' => count($dateRange),
                'showPricingForm' => $this->showPricingForm,
                'hotelDataIncluded' => !empty($this->hotelData),
            ]);

            // Send all necessary data in a single dispatch - this is the key fix for the "two-click" issue
            $this->dispatch('pricingDataReady', [
                'ratePlans' => $this->ratePlansData,
                'roomsData' => $this->roomsData,
                'boardTypesData' => $this->boardTypesData,
                'pricingData' => $this->pricingData,
                'dateRange' => $dateRange,
                'hotelData' => $this->hotelData,
            ]);

            // Hide spinner after data is dispatched
            $this->isLoading = false;
        } catch (\Exception $e) {
            // Hide spinner in case of error
            $this->isLoading = false;
            $this->showPricingForm = false;

            Log::error('Error generating pricing form: ' . $e->getMessage());
            $this->addError('form', 'Fiyat formu oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Refresh pricing data when triggered by Livewire component
     *
     * @return void
     */
    #[On('refreshPricingData')]
    public function refreshPricingData(): void
    {
        try {
            // Log the refresh attempt
            // Refreshing pricing data automatically

            // Make sure we reset the UI state
            $this->isLoading = true;
            $this->showPricingForm = true;

            // Clear any cached pricing data to ensure we fetch fresh data
            $this->pricingData = [];

            // Check if rooms data is empty - reload if necessary
            if (empty($this->roomsData) && $this->hotel) {
                // Room data is empty, reloading rooms and board types before refresh
                $this->loadRoomsAndBoardTypes();
            }

            // Force a complete refresh of the pricing data
            // Force refreshing pricing data with cached data cleared
            $this->generatePricingForm();
        } catch (\Exception $e) {
            Log::error('Error refreshing pricing data: ' . $e->getMessage());
            $this->isLoading = false;

            // Dispatch an error event to the frontend
            $this->dispatch('pricing-form-error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    #[On('pricing-form-loaded')]
    public function handlePricingFormLoaded(): void
    {
        // Make sure to show form and hide loading state
        $this->showPricingForm = true;
        $this->isLoading = false;
    }

    #[On('pricing-form-error')]
    public function handlePricingFormError($data): void
    {
        // Hide loading state and form on error
        $this->isLoading = false;
        $this->showPricingForm = false;

        Log::error('Error from child component: ' . json_encode($data));
    }

    /**
     * Determine refund policy options based on selected room and hotel settings
     *
     * @param int $roomId Room ID
     * @return array Refund policy options
     */
    protected function determineRefundOptions(int $roomId): array
    {
        $options = [
            'allow_refundable' => true,
            'allow_non_refundable' => true,
            'non_refundable_discount' => 0,
            'selected_type' => 'refundable', // Default selected type
        ];

        try {
            // Make sure hotel is loaded
            if (!$this->hotel) {
                return $options;
            }

            // Get hotel's refund policy settings
            $hotelAllowsRefundable = $this->hotel->allow_refundable ?? true;
            $hotelAllowsNonRefundable = $this->hotel->allow_non_refundable ?? true;
            $nonRefundableDiscount = $this->hotel->non_refundable_discount ?? 0;

            // Apply hotel settings
            $options['allow_refundable'] = $hotelAllowsRefundable;
            $options['allow_non_refundable'] = $hotelAllowsNonRefundable;
            $options['non_refundable_discount'] = $nonRefundableDiscount;

            // Get room data
            $room = isset($this->roomsData[$roomId]) ? $this->roomsData[$roomId] : null;

            if (!$room) {
                return $options;
            }

            // If room has specific refund policy settings, override hotel settings
            if (isset($room['allow_refundable'])) {
                $options['allow_refundable'] = $room['allow_refundable'];
            }

            if (isset($room['allow_non_refundable'])) {
                $options['allow_non_refundable'] = $room['allow_non_refundable'];
            }

            if (isset($room['non_refundable_discount'])) {
                $options['non_refundable_discount'] = $room['non_refundable_discount'];
            }

            // Determine default selected type
            if ($options['allow_refundable'] && $options['allow_non_refundable']) {
                // If both are allowed, default to refundable
                $options['selected_type'] = 'refundable';
            } else if ($options['allow_refundable']) {
                $options['selected_type'] = 'refundable';
            } else if ($options['allow_non_refundable']) {
                $options['selected_type'] = 'non_refundable';
            } else {
                // If neither is allowed (should not happen), default to refundable
                $options['selected_type'] = 'refundable';
                $options['allow_refundable'] = true;
            }

            \Log::debug('Determined refund options for room', [
                'room_id' => $roomId,
                'options' => $options
            ]);

            return $options;
        } catch (\Exception $e) {
            \Log::error('Error determining refund options: ' . $e->getMessage(), [
                'room_id' => $roomId,
                'trace' => $e->getTraceAsString()
            ]);
            return $options;
        }
    }

    /**
     * Get data to pass to the view
     *
     * @return array
     */
    protected function getViewData(): array
    {
        try {
            return [
                'roomsCount' => is_array($this->selectedRooms) ? count($this->selectedRooms) : 0,
                'boardTypesCount' => is_array($this->selectedBoardTypes) ? count($this->selectedBoardTypes) : 0,
                'ratePlansCount' => is_array($this->ratePlansData) ? count($this->ratePlansData) : 0,
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting view data: ' . $e->getMessage());
            return [
                'roomsCount' => 0,
                'boardTypesCount' => 0,
                'ratePlansCount' => 0,
            ];
        }
    }

    /**
     * Get navigation badge showing number of rate plans
     * 
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) RatePlan::count();
        } catch (\Exception $e) {
            \Log::error('Error getting navigation badge: ' . $e->getMessage());
            return null;
        }
    }
}