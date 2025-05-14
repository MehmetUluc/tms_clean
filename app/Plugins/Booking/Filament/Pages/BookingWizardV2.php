<?php

namespace App\Plugins\Booking\Filament\Pages;

use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Booking\Models\Guest;
use App\Plugins\Accommodation\Models\Hotel; 
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\Inventory;
use Carbon\Carbon;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Schema;

class BookingWizardV2 extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'New Booking V2';
    protected static ?string $title = 'Advanced Booking Wizard';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Booking Management';
    protected static string $view = 'filament.pages.booking-wizard-v2';
    
    protected static ?string $slug = 'booking-wizard-v2';
    
    public static function canAccess(): bool
    {
        // Super admin her zaman erişebilmeli
        if (auth()->user() && auth()->user()->hasRole('super_admin')) {
            return true;
        }
        
        // Diğer kullanıcılar için yetki kontrolü
        return auth()->user() && auth()->user()->can('create', Reservation::class);
    }

    public array $data = [];
    public $currentStep = 1;
    public $regions = [];
    public $selectedRegion = null;
    public $availableHotels = [];
    public $selectedHotel = null;
    public $checkInDate = null;
    public $checkOutDate = null;
    public $adults = 1;
    public $children = 0;
    public $childrenAges = [];
    public $availableRooms = [];
    public $selectedRoom = null;
    public $selectedBoardType = null;
    public $roomTypeId = null;
    public $guestData = [];
    public $totalAmount = 0;
    
    public function mount(): void
    {
        // Bölge ve region verilerini yükle
        $this->loadRegions();
        
        $this->data = [
            'region_id' => null,
            'hotel_id' => null,
            'check_in_date' => Carbon::today()->addDay()->format('Y-m-d'),
            'check_out_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
            'adults' => 1,
            'children' => 0,
            'children_ages' => [],
            'guest_details' => [
                [
                    'first_name' => '',
                    'last_name' => '',
                    'email' => '',
                    'phone' => '',
                    'is_primary' => true,
                ]
            ],
            'special_requests' => '',
            'payment_method' => 'credit_card',
        ];

        $this->checkInDate = $this->data['check_in_date'];
        $this->checkOutDate = $this->data['check_out_date'];
        $this->adults = $this->data['adults'];
        $this->children = $this->data['children'];
    }
    
    protected function loadRegions(): void
    {
        try {
            // Bölgeleri yükle - en üst seviye (parent_id = null) olanları getir
            $query = Region::query();
            
            // Eğer parent_id kolonu varsa, ona göre filtrele
            if (Schema::hasColumn('regions', 'parent_id')) {
                $query->where(function($q) {
                    $q->whereNull('parent_id')
                      ->orWhere('parent_id', 0);
                });
            }
            
            $this->regions = $query->orderBy('name')
                ->get()
                ->pluck('name', 'id')
                ->toArray();
                
            \Log::debug("BookingWizardV2 - Loaded regions: " . json_encode($this->regions));
        } catch (\Exception $e) {
            \Log::error("BookingWizardV2 - Error loading regions: " . $e->getMessage());
            $this->regions = [];
        }
    }
    
    protected function getHotelsByRegion($regionId): Collection
    {
        // Bölgeye göre otelleri getir
        $region = Region::find($regionId);
        
        if (!$region) {
            return collect();
        }
        
        // Region modelindeki hazır metodu kullan - tüm alt bölgelerdeki otelleri de getirir
        return $region->getAllHotelsAttribute();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Location & Dates')
                        ->schema([
                            Section::make('Select Region & Dates')
                                ->schema([
                                    Select::make('region_id')
                                        ->label('Select Region')
                                        ->options($this->regions)
                                        ->searchable()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            \Log::debug("BookingWizardV2 - Region selected: " . $state);
                                            $this->selectedRegion = $state;
                                            $this->data['region_id'] = $state;
                                            $this->loadHotelsForRegion($state);
                                        }),
                                                                        
                                    Grid::make(2)
                                        ->schema([
                                            DatePicker::make('check_in_date')
                                                ->label('Check-in Date')
                                                ->required()
                                                ->minDate(now())
                                                ->default(fn () => Carbon::today()->addDay())
                                                ->native(false)
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    \Log::debug("BookingWizardV2 - Check-in date updated: " . $state);
                                                    $this->checkInDate = $state;
                                                    $this->data['check_in_date'] = $state;
                                                    
                                                    // Ensure check-out is at least 1 day after check-in
                                                    $checkIn = Carbon::parse($state);
                                                    $checkOut = Carbon::parse($this->data['check_out_date'] ?? null);
                                                    
                                                    if (!$checkOut || $checkOut->lessThanOrEqualTo($checkIn)) {
                                                        $this->data['check_out_date'] = $checkIn->copy()->addDay()->format('Y-m-d');
                                                        $this->checkOutDate = $this->data['check_out_date'];
                                                        $set('check_out_date', $this->data['check_out_date']);
                                                        \Log::debug("BookingWizardV2 - Auto-adjusted check-out date: " . $this->data['check_out_date']);
                                                    }
                                                }),

                                            DatePicker::make('check_out_date')
                                                ->label('Check-out Date')
                                                ->required()
                                                ->minDate(fn (Get $get) => Carbon::parse($get('check_in_date') ?? now())->addDay())
                                                ->default(fn () => Carbon::today()->addDays(2))
                                                ->native(false)
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    \Log::debug("BookingWizardV2 - Check-out date updated: " . $state);
                                                    $this->checkOutDate = $state;
                                                    $this->data['check_out_date'] = $state;
                                                }),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('adults')
                                                ->label('Adults')
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(10)
                                                ->default(1)
                                                ->required()
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    \Log::debug("BookingWizardV2 - Adults updated: " . $state);
                                                    $this->adults = $state;
                                                    $this->data['adults'] = $state;
                                                }),

                                            TextInput::make('children')
                                                ->label('Children')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(10)
                                                ->default(0)
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    \Log::debug("BookingWizardV2 - Children updated: " . $state);
                                                    $this->children = $state;
                                                    $this->data['children'] = $state;
                                                    
                                                    // Eğer çocuk sayısı değiştiyse, çocuk yaşları alanını güncelle
                                                    $this->updateChildrenAges($state);
                                                    $set('children_ages', $this->data['children_ages']);
                                                }),
                                        ]),
                                        
                                    // Çocuk yaşları için alanlar - sadece çocuk varsa gösterilecek
                                    Repeater::make('children_ages')
                                        ->label('Children Ages')
                                        ->schema([
                                            TextInput::make('age')
                                                ->label('Age')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(17)
                                                ->required(),
                                        ])
                                        ->minItems(fn (Get $get) => (int) $get('children'))
                                        ->maxItems(fn (Get $get) => (int) $get('children'))
                                        ->defaultItems(fn (Get $get) => (int) $get('children'))
                                        ->visible(fn (Get $get) => (int) $get('children') > 0)
                                        ->reactive()
                                        ->afterStateUpdated(function ($state) {
                                            $this->data['children_ages'] = $state;
                                            $this->childrenAges = $state;
                                        }),
                                ])
                                ->columnSpan('full'),
                                
                            // Otel Listesi Kısmı - Region seçildiyse gösterilecek
                            Section::make('Available Hotels')
                                ->schema([
                                    ViewField::make('available_hotels')
                                        ->view('filament.pages.booking-wizard-v2.available-hotels')
                                        ->visible(fn (Get $get) => $get('region_id') !== null),
                                ])
                                ->visible(fn (Get $get) => filled($get('region_id')))
                                ->columnSpan('full'),
                        ]),
                        
                    Step::make('Room Selection')
                        ->schema([
                            Section::make('Select a Room')
                                ->schema([
                                    ViewField::make('available_rooms')
                                        ->view('filament.pages.booking-wizard-v2.room-selection'),
                                    
                                    Placeholder::make('selected_room_info')
                                        ->label('Selected Room')
                                        ->content(fn() => $this->getSelectedRoomInfo())
                                        ->visible(fn() => !empty($this->data['room_id']))
                                ]),
                        ]),
                        
                    Step::make('Guest Information')
                        ->schema([
                            Section::make('Primary Guest')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('guest_details.0.first_name')
                                                ->label('First Name')
                                                ->required(),
                                                
                                            TextInput::make('guest_details.0.last_name')
                                                ->label('Last Name')
                                                ->required(),
                                        ]),
                                        
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('guest_details.0.email')
                                                ->label('Email')
                                                ->email()
                                                ->required(),
                                                
                                            TextInput::make('guest_details.0.phone')
                                                ->label('Phone')
                                                ->tel()
                                                ->required(),
                                        ]),
                                ]),
                                
                            Section::make('Additional Guests')
                                ->schema([
                                    Repeater::make('additional_guests')
                                        ->label('Additional Guests')
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    TextInput::make('first_name')
                                                        ->label('First Name')
                                                        ->required(),
                                                        
                                                    TextInput::make('last_name')
                                                        ->label('Last Name')
                                                        ->required(),
                                                ]),
                                        ])
                                        ->minItems(0)
                                        ->maxItems(fn (Get $get) => ((int) $get('adults') - 1) + (int) $get('children'))
                                        ->defaultItems(0)
                                        ->createItemButtonLabel('Add Guest')
                                        ->collapsible()
                                        ->afterStateUpdated(function ($state) {
                                            $this->data['additional_guests'] = $state;
                                        }),
                                ]),
                                
                            Section::make('Special Requests')
                                ->schema([
                                    TextInput::make('special_requests')
                                        ->label('Special Requests or Notes')
                                        ->placeholder('Enter any special requests or requirements...')
                                        ->maxLength(255),
                                ]),
                        ]),
                        
                    Step::make('Payment & Confirmation')
                        ->schema([
                            Section::make('Booking Summary')
                                ->schema([
                                    ViewField::make('booking_summary')
                                        ->view('filament.pages.booking-wizard-v2.booking-summary')
                                        ->viewData([
                                            'hotel' => $this->getSelectedHotelData(),
                                            'room' => $this->getSelectedRoomData(),
                                            'check_in' => $this->data['check_in_date'] ?? now()->format('Y-m-d'),
                                            'check_out' => $this->data['check_out_date'] ?? now()->addDay()->format('Y-m-d'),
                                            'adults' => $this->data['adults'] ?? 1,
                                            'children' => $this->data['children'] ?? 0,
                                            'nights' => isset($this->data['check_in_date'], $this->data['check_out_date']) 
                                                ? Carbon::parse($this->data['check_out_date'])->diffInDays(Carbon::parse($this->data['check_in_date']))
                                                : 1,
                                            'total_amount' => $this->totalAmount,
                                            'primary_guest' => $this->data['guest_details'][0] ?? null,
                                        ]),
                                ]),
                                
                            Section::make('Payment Information')
                                ->schema([
                                    Select::make('payment_method')
                                        ->label('Payment Method')
                                        ->options([
                                            'credit_card' => 'Credit Card',
                                            'bank_transfer' => 'Bank Transfer',
                                            'cash' => 'Cash',
                                            'pay_at_hotel' => 'Pay at Hotel',
                                        ])
                                        ->default('credit_card')
                                        ->required(),
                                        
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('amount_paid')
                                                ->label('Amount Paid')
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->maxValue(fn() => $this->totalAmount)
                                                ->postfix('TL')
                                                ->reactive()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    $state = (float)$state;
                                                    $balanceDue = $this->totalAmount - $state;
                                                    $set('balance_due', $balanceDue);
                                                }),
                                                
                                            TextInput::make('balance_due')
                                                ->label('Balance Due')
                                                ->numeric()
                                                ->default(fn() => $this->totalAmount)
                                                ->disabled()
                                                ->postfix('TL'),
                                        ])
                                        ->visible(fn (Get $get) => $get('payment_method') !== 'pay_at_hotel'),
                                ]),
                                
                            Section::make('')
                                ->schema([
                                    Grid::make(1)
                                        ->schema([
                                            Placeholder::make('reservation_confirmation')
                                                ->label('Confirm your reservation')
                                                ->content('Please review all details above before confirming your reservation. By clicking the button below, you agree to our terms and conditions.'),
                                                
                                            Placeholder::make('confirmation_button')
                                                ->content(function (): HtmlString {
                                                    return new HtmlString('
                                                        <button 
                                                            type="button" 
                                                            x-on:click="$wire.confirmReservation()" 
                                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                                        >
                                                            Confirm and Complete Reservation
                                                        </button>
                                                    ');
                                                }),
                                        ]),
                                ]),
                        ]),
                ])
                ->skippable(false)
                ->persistStepInQueryString()
            ])
            ->statePath('data');
    }
    
    public function loadHotelsForRegion($regionId): void
    {
        if (!$regionId) {
            $this->availableHotels = [];
            return;
        }
        
        \Log::debug("BookingWizardV2 - Loading hotels for region: " . $regionId);
        
        $hotels = $this->getHotelsByRegion($regionId);
        
        // Müsaitlik kontrolü için tarih bilgilerini kullan
        $checkIn = Carbon::parse($this->checkInDate);
        $checkOut = Carbon::parse($this->checkOutDate);
        
        // Şimdilik otelleri direkt göster, sonradan müsaitlik kontrolü eklenecek
        $this->availableHotels = $hotels->map(function ($hotel) {
            // Kapak resmi URL'sini doğru oluştur
            $coverImage = null;
            if (!empty($hotel->cover_image)) {
                if (filter_var($hotel->cover_image, FILTER_VALIDATE_URL)) {
                    $coverImage = $hotel->cover_image;
                } else {
                    $coverImage = '/storage/' . $hotel->cover_image;
                }
            }
            
            // En ucuz oda fiyatını tespit et (gerçek veri)
            $basePrice = 0;
            $cheapestRoom = $hotel->rooms()->orderBy('price', 'asc')->first();
            if ($cheapestRoom) {
                $basePrice = $cheapestRoom->price;
            } else {
                $basePrice = rand(500, 2000); // Eğer oda yoksa rastgele fiyat göster
            }
            
            return [
                'id' => $hotel->id,
                'name' => $hotel->name,
                'description' => $hotel->description,
                'cover_image' => $coverImage,
                'star_rating' => $hotel->stars ?? 4,
                'address' => $hotel->getFullAddressAttribute(),
                'is_available' => true, // Şimdilik hepsi müsait
                'base_price' => $basePrice,
                'room_count' => $hotel->rooms()->count(),
            ];
        })->toArray();
        
        \Log::debug("BookingWizardV2 - Loaded " . count($this->availableHotels) . " hotels");
    }
    
    protected function updateChildrenAges($childrenCount): void
    {
        $childrenCount = (int) $childrenCount;
        $currentAges = $this->data['children_ages'] ?? [];
        
        // Eğer çocuk yoksa, boş dizi yap
        if ($childrenCount === 0) {
            $this->data['children_ages'] = [];
            return;
        }
        
        // Eğer yeni çocuk sayısı mevcut yaş dizisinden büyükse, yeni boş yaşlar ekle
        if ($childrenCount > count($currentAges)) {
            for ($i = count($currentAges); $i < $childrenCount; $i++) {
                $currentAges[] = ['age' => ''];
            }
        } 
        // Eğer azaldıysa, fazla olanları kaldır
        else if ($childrenCount < count($currentAges)) {
            $currentAges = array_slice($currentAges, 0, $childrenCount);
        }
        
        $this->data['children_ages'] = $currentAges;
    }
    
    public function selectHotel($hotelId): void
    {
        \Log::debug("BookingWizardV2 - Hotel selected: " . $hotelId);
        
        $this->selectedHotel = $hotelId;
        $this->data['hotel_id'] = $hotelId;
        
        // Bir sonraki adıma geç
        $this->nextStep();
    }
    
    public function nextStep(): void
    {
        \Log::debug("BookingWizardV2 - Moving to next step from: " . $this->currentStep);
        
        // Adım validasyonları
        if ($this->currentStep === 1) {
            // Bölge ve tarih kontrolü
            if (empty($this->data['region_id'])) {
                Notification::make()
                    ->title('Please select a region')
                    ->warning()
                    ->send();
                return;
            }
            
            // Otel seçildi mi?
            if (empty($this->data['hotel_id'])) {
                Notification::make()
                    ->title('Please select a hotel')
                    ->warning()
                    ->send();
                return;
            }
        }
        
        // Diğer adım validasyonları burada eklenecek
        
        $this->currentStep++;
        
        // Adıma göre yükleme işlemleri
        if ($this->currentStep === 2) {
            // Odaları yükle
            $this->loadRoomsForHotel();
        }
        
        Notification::make()
            ->title('Moving to step ' . $this->currentStep)
            ->success()
            ->send();
    }
    
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            
            Notification::make()
                ->title('Going back to step ' . $this->currentStep)
                ->success()
                ->send();
        }
    }
    
    public function loadRoomsForHotel(): void
    {
        \Log::debug("BookingWizardV2 - Loading rooms for hotel: " . $this->selectedHotel);
        
        if (!$this->selectedHotel) {
            $this->availableRooms = [];
            return;
        }
        
        $hotel = Hotel::find($this->selectedHotel);
        
        if (!$hotel) {
            $this->availableRooms = [];
            \Log::warning("BookingWizardV2 - Hotel not found: " . $this->selectedHotel);
            return;
        }
        
        // Gerçek oda verilerini yükle
        // Otel için doğru odaları manuel olarak sorgu yap (namespace sorununu çözmek için)
        $rooms = Room::with(['roomType', 'amenities', 'boardTypes'])
            ->where('hotel_id', $hotel->id)
            ->get();
        
        // Odaları düzenleyelim ve ek bilgiler ekleyelim
        $this->availableRooms = $rooms->map(function ($room) {
            // Odaya ait board tipleri (sadece etkinler)
            $boardTypes = $room->boardTypes->map(function ($boardType) {
                return [
                    'id' => $boardType->id,
                    'name' => $boardType->name,
                    'code' => $boardType->code,
                    'price_modifier' => 0,
                ];
            })->toArray();
            
            // Odanın özellikleri
            $amenities = $room->amenities->pluck('name')->toArray();
            
            // Oda resmini düzelt
            $image = null;
            if (!empty($room->cover_image)) {
                if (filter_var($room->cover_image, FILTER_VALIDATE_URL)) {
                    $image = $room->cover_image;
                } else {
                    $image = '/storage/' . $room->cover_image;
                }
            }
            
            return [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
                'type' => $room->roomType->name ?? 'Standard Room',
                'max_occupancy' => $room->max_occupancy ?? 2,
                'price_per_night' => $room->price ?? 0,
                'image' => $image,
                'board_types' => !empty($boardTypes) ? $boardTypes : [
                    ['id' => 1, 'name' => 'Room Only', 'code' => 'RO', 'price_modifier' => 0],
                ],
                'features' => !empty($amenities) ? $amenities : ['Basic Amenities'],
                'is_smoking' => $room->is_smoking ?? false,
                'size' => $room->size ?? null,
                'view' => $room->view ?? null,
            ];
        })->toArray();
        
        // Eğer oda bulunamadıysa örnek veriler gösterelim
        if (empty($this->availableRooms)) {
            $this->availableRooms = [
                [
                    'id' => 9999,
                    'name' => 'Standard Room',
                    'description' => 'Comfortable room with all basic amenities.',
                    'type' => 'Standard',
                    'max_occupancy' => 2,
                    'price_per_night' => 850,
                    'image' => null,
                    'board_types' => [
                        ['id' => 1, 'name' => 'Room Only', 'code' => 'RO', 'price_modifier' => 0],
                        ['id' => 2, 'name' => 'Bed & Breakfast', 'code' => 'BB', 'price_modifier' => 150],
                    ],
                    'features' => ['Air Conditioning', 'TV', 'Free WiFi'],
                    'is_smoking' => false,
                ],
            ];
            \Log::warning("BookingWizardV2 - No rooms found for hotel, showing sample data");
        }
        
        \Log::debug("BookingWizardV2 - Loaded " . count($this->availableRooms) . " rooms");
    }
    
    public function selectRoom($roomId, $boardTypeId = null): void
    {
        \Log::debug("BookingWizardV2 - Room selected: " . $roomId);
        
        $this->selectedRoom = $roomId;
        $this->data['room_id'] = $roomId;
        
        // Board type seçili değilse ilk board type'ı seç
        if (!$boardTypeId) {
            $room = collect($this->availableRooms)->firstWhere('id', $roomId);
            if ($room && !empty($room['board_types'])) {
                $this->selectedBoardType = $room['board_types'][0]['id'];
                $this->data['board_type_id'] = $this->selectedBoardType;
            }
        } else {
            $this->selectedBoardType = $boardTypeId;
            $this->data['board_type_id'] = $boardTypeId;
        }
        
        // Toplam fiyatı güncelle
        $this->calculateTotalAmount();
        
        // Bir sonraki adıma geç
        $this->nextStep();
    }
    
    public function getSelectedRoomInfo(): string
    {
        if (empty($this->data['room_id'])) {
            return 'No room selected';
        }
        
        $room = collect($this->availableRooms)->firstWhere('id', $this->data['room_id']);
        
        if (!$room) {
            return 'Selected room information is not available';
        }
        
        // Seçilen board type'ı bul
        $boardType = collect($room['board_types'])->firstWhere('id', $this->data['board_type_id'] ?? null);
        $boardTypeName = $boardType ? $boardType['name'] : 'No board type selected';
        
        // Tarih aralığını hesapla
        $checkIn = Carbon::parse($this->data['check_in_date']);
        $checkOut = Carbon::parse($this->data['check_out_date']);
        $nights = $checkOut->diffInDays($checkIn);
        
        // Toplam fiyatı hesapla - price_modifier'ı şimdilik 0 olarak alıyoruz
        $totalPrice = $room['price_per_night'] * $nights;
        
        // Bilgileri biçimlendir
        return "
            <div class='space-y-2'>
                <p><strong>Room:</strong> {$room['name']} ({$room['type']})</p>
                <p><strong>Board:</strong> {$boardTypeName}</p>
                <p><strong>Nights:</strong> {$nights}</p>
                <p><strong>Price per night:</strong> " . number_format($room['price_per_night'], 0) . " TL</p>
                <p><strong>Total Price:</strong> " . number_format($totalPrice, 0) . " TL</p>
            </div>
        ";
    }
    
    protected function calculateTotalAmount(): void
    {
        // Toplam fiyatı hesapla
        if (empty($this->data['room_id'])) {
            $this->totalAmount = 0;
            return;
        }
        
        $room = collect($this->availableRooms)->firstWhere('id', $this->data['room_id']);
        
        if (!$room) {
            $this->totalAmount = 0;
            return;
        }
        
        // Seçilen board type'ı bul
        $boardType = collect($room['board_types'])->firstWhere('id', $this->data['board_type_id'] ?? null);
        // Board type price_modifier'ı şimdilik 0 olarak alıyoruz
        $boardTypeModifier = 0;
        
        // Tarih aralığını hesapla
        $checkIn = Carbon::parse($this->data['check_in_date']);
        $checkOut = Carbon::parse($this->data['check_out_date']);
        $nights = $checkOut->diffInDays($checkIn);
        
        // Toplam fiyatı hesapla
        $this->totalAmount = ($room['price_per_night'] + $boardTypeModifier) * $nights;
        $this->data['total_amount'] = $this->totalAmount;
        $this->data['nights'] = $nights;
        
        \Log::debug("BookingWizardV2 - Total amount calculated: " . $this->totalAmount);
    }
    
    public function getSelectedHotelData(): ?array
    {
        if (empty($this->data['hotel_id'])) {
            return null;
        }
        
        $hotelId = $this->data['hotel_id'];
        $hotel = collect($this->availableHotels)->firstWhere('id', $hotelId);
        
        if (!$hotel) {
            $actualHotel = Hotel::find($hotelId);
            if ($actualHotel) {
                return [
                    'id' => $actualHotel->id,
                    'name' => $actualHotel->name,
                    'address' => $actualHotel->getFullAddressAttribute(),
                    'star_rating' => $actualHotel->stars ?? 0,
                ];
            }
            return null;
        }
        
        return $hotel;
    }
    
    public function getSelectedRoomData(): ?array
    {
        if (empty($this->data['room_id'])) {
            return null;
        }
        
        $roomId = $this->data['room_id'];
        $room = collect($this->availableRooms)->firstWhere('id', $roomId);
        
        if (!$room) {
            // Eğer hafızada yoksa veritabanından alalım
            // App\Plugins\Accommodation\Models\Room namespace'ini kullanıyoruz
            $actualRoom = Room::with(['roomType', 'boardTypes'])->find($roomId);
            if ($actualRoom) {
                // Board type'ı da alalım
                $boardType = null;
                if (!empty($this->data['board_type_id'])) {
                    $boardType = $actualRoom->boardTypes->where('id', $this->data['board_type_id'])->first();
                }
                
                return [
                    'id' => $actualRoom->id,
                    'name' => $actualRoom->name,
                    'type' => $actualRoom->roomType?->name ?? 'Standard Room',
                    'price_per_night' => $actualRoom->price ?? 0,
                    'board_type' => $boardType ? [
                        'id' => $boardType->id,
                        'name' => $boardType->name,
                        'code' => $boardType->code,
                        'price_modifier' => 0,
                    ] : null,
                ];
            }
            return null;
        }
        
        // Board type'ı ekle
        if (!empty($this->data['board_type_id'])) {
            $boardTypeId = $this->data['board_type_id'];
            $boardType = collect($room['board_types'])->firstWhere('id', $boardTypeId);
            $room['selected_board_type'] = $boardType;
        }
        
        return $room;
    }
    
    public function confirmReservation(): void
    {
        \Log::debug("BookingWizardV2 - Confirming reservation");
        
        // Tüm alanları validate et
        $this->validate();
        
        try {
            // Rezervasyon verisini hazırla
            $reservationData = [
                'hotel_id' => $this->data['hotel_id'],
                'room_id' => $this->data['room_id'],
                'board_type_id' => $this->data['board_type_id'] ?? null,
                'check_in' => $this->data['check_in_date'],
                'check_out' => $this->data['check_out_date'],
                'adults' => $this->data['adults'],
                'children' => $this->data['children'],
                'status' => 'confirmed',
                'payment_method' => $this->data['payment_method'] ?? 'credit_card',
                'amount_paid' => $this->data['amount_paid'] ?? 0,
                'balance_due' => $this->data['balance_due'] ?? $this->totalAmount,
                'total_amount' => $this->totalAmount,
                'special_requests' => $this->data['special_requests'] ?? null,
                'created_by' => auth()->id() ?? 1,
            ];
            
            // Yeni rezervasyon oluştur
            $reservation = Reservation::create($reservationData);
            
            // Ana misafiri ekle
            if (!empty($this->data['guest_details'][0])) {
                $primaryGuestData = $this->data['guest_details'][0];
                $primaryGuestData['is_primary'] = true;
                
                $primaryGuest = new Guest($primaryGuestData);
                $reservation->guests()->save($primaryGuest);
            }
            
            // Ek misafirleri ekle
            if (!empty($this->data['additional_guests'])) {
                foreach ($this->data['additional_guests'] as $guestData) {
                    $guestData['is_primary'] = false;
                    $guest = new Guest($guestData);
                    $reservation->guests()->save($guest);
                }
            }
            
            // Başarılı bildirim
            Notification::make()
                ->title('Reservation Created Successfully')
                ->body('Your reservation has been created with number: ' . $reservation->id)
                ->success()
                ->send();
                
            // Formu sıfırla ve ilk adıma dön
            $this->reset('data', 'currentStep', 'selectedHotel', 'selectedRoom', 'selectedBoardType', 'availableHotels', 'availableRooms');
            $this->currentStep = 1;
            
            // Yeni veri oluştur
            $this->data = [
                'region_id' => null,
                'hotel_id' => null,
                'check_in_date' => Carbon::today()->addDay()->format('Y-m-d'),
                'check_out_date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'adults' => 1,
                'children' => 0,
                'children_ages' => [],
                'guest_details' => [
                    [
                        'first_name' => '',
                        'last_name' => '',
                        'email' => '',
                        'phone' => '',
                        'is_primary' => true,
                    ]
                ],
                'special_requests' => '',
                'payment_method' => 'credit_card',
            ];
            
            $this->loadRegions();
            
        } catch (\Exception $e) {
            // Hata bildirimi
            \Log::error("BookingWizardV2 - Error creating reservation: " . $e->getMessage());
            
            Notification::make()
                ->title('Error Creating Reservation')
                ->body('There was an error creating your reservation: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        // Super admin her zaman erişebilmeli
        if (auth()->user() && auth()->user()->hasRole('super_admin')) {
            return true;
        }
        
        // Diğer kullanıcılar için yetki kontrolü
        return auth()->user() && auth()->user()->can('create', Reservation::class);
    }
}