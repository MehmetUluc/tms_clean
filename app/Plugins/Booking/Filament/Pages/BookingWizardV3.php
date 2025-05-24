<?php

namespace App\Plugins\Booking\Filament\Pages;

use App\Models\User;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Region;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Booking\Models\Guest;
use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Booking\Filament\Resources\ReservationResource;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Models\DailyRate;
use App\Plugins\Pricing\Services\ChildPolicyPricingService;
use App\Plugins\Pricing\Services\InventoryService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Computed;

class BookingWizardV3 extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'New Booking V3';
    protected static ?string $title = 'Create Booking';
    protected static ?int $navigationSort = 15;
    protected static string $view = 'booking::pages.booking-wizard-v3';

    public ?array $data = [];
    
    // Search & Availability
    public ?string $destination = null;
    public ?string $checkIn = null;
    public ?string $checkOut = null;
    public int $adults = 1;
    public int $children = 0;
    public array $childrenAges = [];
    public ?string $promoCode = null;
    
    // Hotel & Room Selection
    public array $selectedRooms = [];
    public ?int $selectedHotelId = null;
    
    // Guest Details
    public array $guestDetails = [];
    public ?string $specialRequests = null;
    public bool $airportTransfer = false;
    public bool $travelInsurance = false;
    public ?string $loyaltyNumber = null;
    
    // Payment
    public string $paymentMethod = 'credit_card';
    public bool $acceptTerms = false;
    
    // Filters
    public array $priceRange = [0, 5000];
    public array $starRatings = [];
    public array $boardTypes = [];
    public array $amenities = [];
    public string $sortBy = 'recommended';

    // Query string'i kaldırdık, sadece state kullanacağız
    // protected $queryString = [];

    public function mount(): void
    {
        // Set default dates if not provided
        if (!$this->checkIn) {
            $this->checkIn = now()->addDays(7)->format('Y-m-d');
        }
        if (!$this->checkOut) {
            $this->checkOut = now()->addDays(8)->format('Y-m-d');
        }
        
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $formData = [
            'destination' => $this->destination,
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'adults' => $this->adults,
            'children' => $this->children,
            'promo_code' => $this->promoCode,
        ];
        
        // Child ages'leri ayrı ayrı ekle
        foreach ($this->childrenAges as $i => $age) {
            $formData["child_age_{$i}"] = $age;
        }
        
        $this->form->fill($formData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Search & Availability')
                        ->icon('heroicon-o-magnifying-glass')
                        ->description('Find your perfect stay')
                        ->schema($this->getSearchSchema())
                        ->afterValidation(function () {
                            // Clear selected rooms when search criteria changes
                            $this->selectedRooms = [];
                            $this->selectedHotelId = null;
                        }),
                        
                    Wizard\Step::make('Hotel & Room Selection')
                        ->icon('heroicon-o-building-office')
                        ->description('Choose your accommodation')
                        ->schema($this->getHotelSelectionSchema())
                        ->beforeValidation(function () {
                            // Reset selected hotel when going back
                            if (empty($this->selectedRooms)) {
                                $this->selectedHotelId = null;
                            }
                        })
                        ->afterStateHydrated(function () {
                            // Ensure destination is properly set when entering this step
                            \Log::info('BookingWizard - Entering Hotel Selection Step', [
                                'destination' => $this->destination,
                                'checkIn' => $this->checkIn,
                                'checkOut' => $this->checkOut,
                                'adults' => $this->adults,
                                'children' => $this->children,
                            ]);
                        }),
                        
                    Wizard\Step::make('Guest Details')
                        ->icon('heroicon-o-users')
                        ->description('Who\'s traveling')
                        ->schema($this->getGuestDetailsSchema()),
                        
                    Wizard\Step::make('Review & Payment')
                        ->icon('heroicon-o-credit-card')
                        ->description('Complete your booking')
                        ->schema($this->getPaymentSummarySchema()),
                ])
                ->submitAction(new HtmlString('<button type="submit" class="fi-btn fi-btn-size-md fi-btn-color-success">Complete Booking</button>'))
            ])
            ->statePath('data');
    }

    protected function getSearchSchema(): array
    {
        return [
            // Hero Section with Background
            Forms\Components\Section::make()
                ->heading('Find Your Perfect Stay')
                ->description('Search from thousands of hotels and destinations')
                ->schema([
                    // Destination Search
                    Forms\Components\Select::make('destination')
                        ->label('Destination')
                        ->placeholder('Where do you want to go?')
                        ->prefixIcon('heroicon-o-magnifying-glass')
                        ->options(function() {
                            // Tüm region'ları hiyerarşik olarak getir
                            $regions = Region::query()
                                ->with('parent.parent.parent')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function ($region) {
                                    $label = "📍 {$region->name}";
                                    
                                    // Tam hiyerarşi yolunu oluştur
                                    $path = [];
                                    $current = $region->parent;
                                    while ($current) {
                                        array_unshift($path, $current->name);
                                        $current = $current->parent;
                                    }
                                    
                                    if (!empty($path)) {
                                        $label .= " (" . implode(' > ', $path) . ")";
                                    }
                                    
                                    return [(string)$region->id => $label];  // ID'yi string'e çevir
                                })
                                ->toArray();
                            
                            return $regions;
                        })
                        ->searchable()
                        ->searchPrompt('Search for a city, region...')
                        ->searchDebounce(300)
                        ->required()
                        ->preload()
                        ->live()
                        ->default($this->destination)  // Default değer ekle
                        ->afterStateUpdated(function ($state, Set $set) {
                            $this->destination = $state;
                            
                            // Session'a kaydet
                            session(['booking_destination' => $state]);
                            
                            \Log::info('BookingWizard - Destination Selected', [
                                'selected_value' => $state,
                                'state_type' => gettype($state),
                                'is_numeric' => is_numeric($state),
                                'this->destination' => $this->destination,
                                'session_destination' => session('booking_destination'),
                            ]);
                            
                            // Clear selected hotel when destination changes
                            $this->selectedHotelId = null;
                            $this->selectedRooms = [];
                        })
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'destination-search-field']),
                    
                    // Dates and Guests Row
                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\DatePicker::make('check_in')
                                ->label('Check-in Date')
                                ->prefixIcon('heroicon-o-calendar')
                                ->required()
                                ->native(false)
                                ->displayFormat('d M Y')
                                ->minDate(now())
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    $this->checkIn = $state;
                                    $checkOut = $get('check_out');
                                    if ($checkOut && Carbon::parse($state)->gte($checkOut)) {
                                        $newCheckOut = Carbon::parse($state)->addDay()->format('Y-m-d');
                                        $set('check_out', $newCheckOut);
                                        $this->checkOut = $newCheckOut;
                                    }
                                }),
                                
                            Forms\Components\DatePicker::make('check_out')
                                ->label('Check-out Date')
                                ->prefixIcon('heroicon-o-calendar')
                                ->required()
                                ->native(false)
                                ->displayFormat('d M Y')
                                ->minDate(fn (Get $get) => $get('check_in') ? Carbon::parse($get('check_in'))->addDay() : now()->addDay())
                                ->live()
                                ->afterStateUpdated(fn ($state) => $this->checkOut = $state),
                                
                            Forms\Components\TextInput::make('adults')
                                ->label('Adults')
                                ->prefixIcon('heroicon-o-user-group')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(10)
                                ->default(1)
                                ->required()
                                ->live()
                                ->suffixAction(
                                    Forms\Components\Actions\Action::make('increment')
                                        ->icon('heroicon-m-plus')
                                        ->size('sm')
                                        ->action(function (Set $set, Get $get) {
                                            $current = intval($get('adults') ?? 1);
                                            if ($current < 10) {
                                                $set('adults', $current + 1);
                                                $this->adults = $current + 1;
                                            }
                                        })
                                )
                                ->prefixAction(
                                    Forms\Components\Actions\Action::make('decrement')
                                        ->icon('heroicon-m-minus')
                                        ->size('sm')
                                        ->action(function (Set $set, Get $get) {
                                            $current = intval($get('adults') ?? 1);
                                            if ($current > 1) {
                                                $set('adults', $current - 1);
                                                $this->adults = $current - 1;
                                            }
                                        })
                                )
                                ->extraAttributes(['class' => 'guest-counter']),
                            
                            Forms\Components\TextInput::make('children')
                                ->label('Children')
                                ->prefixIcon('heroicon-o-face-smile')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(5)
                                ->default(0)
                                ->live()
                                ->suffixAction(
                                    Forms\Components\Actions\Action::make('increment')
                                        ->icon('heroicon-m-plus')
                                        ->size('sm')
                                        ->action(function (Set $set, Get $get) {
                                            $current = intval($get('children') ?? 0);
                                            if ($current < 5) {
                                                $set('children', $current + 1);
                                                $this->children = $current + 1;
                                            }
                                        })
                                )
                                ->prefixAction(
                                    Forms\Components\Actions\Action::make('decrement')
                                        ->icon('heroicon-m-minus')
                                        ->size('sm')
                                        ->action(function (Set $set, Get $get) {
                                            $current = intval($get('children') ?? 0);
                                            if ($current > 0) {
                                                $set('children', $current - 1);
                                                $this->children = $current - 1;
                                            }
                                        })
                                )
                                ->extraAttributes(['class' => 'guest-counter']),
                        ]),
                        
                    // Children Ages Section
                    Forms\Components\Section::make('Children Ages')
                        ->description('Please specify the age of each child')
                        ->schema(function (Get $get) {
                            $childrenCount = intval($get('children') ?? 0);
                            $fields = [];
                            
                            if ($childrenCount > 0) {
                                $schema = [];
                                for ($i = 0; $i < $childrenCount; $i++) {
                                    $schema[] = Forms\Components\Select::make("child_age_{$i}")
                                        ->label("Child " . ($i + 1))
                                        ->options(array_combine(range(0, 17), array_map(fn($age) => $age . ' years', range(0, 17))))
                                        ->default($this->childrenAges[$i] ?? 5)
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state) use ($i) {
                                            $this->childrenAges[$i] = intval($state);
                                        });
                                }
                                return $schema;
                            }
                            
                            return [
                                Forms\Components\Placeholder::make('no_children')
                                    ->label('')
                                    ->content(new HtmlString('<div class="text-center text-gray-500 py-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                        <p class="mt-2">Add children to specify their ages</p>
                                    </div>'))
                            ];
                        })
                        ->visible(fn (Get $get) => intval($get('children') ?? 0) > 0)
                        ->columns([
                            'default' => 2,
                            'sm' => 3,
                            'lg' => 4,
                        ])
                        ->compact(),
                        
                    // Promo Code Section
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('promo_code')
                                ->label('Have a promo code?')
                                ->placeholder('Enter code')
                                ->prefixIcon('heroicon-o-tag')
                                ->live()
                                ->afterStateUpdated(fn ($state) => $this->promoCode = $state)
                                ->columnSpan(1),
                                
                            // Summary Card
                            Forms\Components\Placeholder::make('booking_summary')
                                ->label('')
                                ->content(function () {
                                    $nights = $this->getNights();
                                    $totalGuests = $this->adults + $this->children;
                                    
                                    return new HtmlString('
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-sm font-medium">' . $nights . ' ' . ($nights === 1 ? 'Night' : 'Nights') . '</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    <span class="text-sm font-medium">' . $totalGuests . ' ' . ($totalGuests === 1 ? 'Guest' : 'Guests') . '</span>
                                                </div>
                                            </div>
                                        </div>
                                    ');
                                })
                                ->columnSpan(1),
                        ]),
                ])
                ->extraAttributes([
                    'class' => 'search-step-section'
                ]),
        ];
    }

    protected function getHotelSelectionSchema(): array
    {
        return [
            Forms\Components\ViewField::make('hotel_search_results')
                ->view('booking::components.hotel-search-v3')
                ->viewData([
                    'hotels' => $this->getAvailableHotels(),
                    'filters' => [
                        'priceRange' => $this->priceRange,
                        'starRatings' => $this->starRatings,
                        'boardTypes' => $this->boardTypes,
                        'amenities' => $this->amenities,
                        'sortBy' => $this->sortBy,
                    ],
                    'selectedHotelId' => $this->selectedHotelId,
                    'selectedRooms' => $this->selectedRooms,
                    'checkIn' => $this->checkIn,
                    'checkOut' => $this->checkOut,
                    'adults' => $this->adults,
                    'children' => $this->children,
                    'childrenAges' => $this->childrenAges,
                    'nights' => $this->getNights(),
                    'destination' => $this->destination,
                    'component' => $this,
                    'inventoryService' => app(InventoryService::class),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getGuestDetailsSchema(): array
    {
        $totalGuests = $this->adults + $this->children;
        $schema = [];
        
        // Room Selection Summary at the top
        if (!empty($this->selectedRooms)) {
            $hotel = $this->getSelectedHotel();
            $nights = $this->getNights();
            
            $summaryHtml = '<div class="guest-form-section">';
            $summaryHtml .= '<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">';
            $summaryHtml .= '<h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center">';
            $summaryHtml .= '<svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
            $summaryHtml .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
            $summaryHtml .= '</svg>Your Selection Summary</h3>';
            
            if ($hotel) {
                $summaryHtml .= '<div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">';
                $summaryHtml .= '<p class="font-medium text-base">' . e($hotel->name) . ' ' . str_repeat('⭐', $hotel->star_rating) . '</p>';
                $summaryHtml .= '<p class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>' . e($hotel->region->name ?? 'Unknown location') . '</p>';
                $summaryHtml .= '<p class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>' . Carbon::parse($this->checkIn)->format('d M Y') . ' - ' . Carbon::parse($this->checkOut)->format('d M Y') . ' (' . $nights . ' ' . ($nights === 1 ? 'night' : 'nights') . ')</p>';
                $summaryHtml .= '<p class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>' . $this->adults . ' ' . ($this->adults === 1 ? 'Adult' : 'Adults');
                if ($this->children > 0) {
                    $summaryHtml .= ', ' . $this->children . ' ' . ($this->children === 1 ? 'Child' : 'Children');
                }
                $summaryHtml .= '</p>';
                $summaryHtml .= '</div>';
                
                // Selected rooms
                $summaryHtml .= '<div class="mt-3 pt-3 border-t border-blue-200 dark:border-blue-800">';
                $summaryHtml .= '<p class="font-medium text-sm mb-2">Selected Rooms:</p>';
                $summaryHtml .= '<ul class="space-y-1">';
                foreach ($this->selectedRooms as $room) {
                    $summaryHtml .= '<li class="text-sm flex items-center justify-between">';
                    $summaryHtml .= '<span><span class="font-medium">' . e($room['room_name']) . '</span> - ' . e($room['board_type_name']) . '</span>';
                    $summaryHtml .= '<span class="font-semibold">₺' . number_format($room['total_price'], 2) . '</span>';
                    $summaryHtml .= '</li>';
                }
                $summaryHtml .= '</ul>';
                $summaryHtml .= '</div>';
            }
            
            $summaryHtml .= '</div>';
            $summaryHtml .= '</div>';
            
            $schema[] = Forms\Components\Placeholder::make('selection_summary')
                ->label('')
                ->content(new HtmlString($summaryHtml));
        }
        
        // Primary Guest with enhanced styling
        $schema[] = Forms\Components\Section::make('Primary Guest')
            ->description('This person will be the main contact for the booking')
            ->icon('heroicon-o-user')
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('guest.0.first_name')
                            ->label('First Name')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->placeholder('John')
                            ->validationMessages([
                                'required' => 'Please enter the first name',
                            ]),
                        Forms\Components\TextInput::make('guest.0.last_name')
                            ->label('Last Name')
                            ->prefixIcon('heroicon-o-user')
                            ->required()
                            ->placeholder('Doe')
                            ->validationMessages([
                                'required' => 'Please enter the last name',
                            ]),
                        Forms\Components\TextInput::make('guest.0.email')
                            ->label('Email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->required()
                            ->placeholder('john@example.com')
                            ->validationMessages([
                                'required' => 'Please enter an email address',
                                'email' => 'Please enter a valid email address',
                            ]),
                    ]),
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('guest.0.phone')
                            ->label('Phone')
                            ->prefixIcon('heroicon-o-phone')
                            ->tel()
                            ->required()
                            ->placeholder('+90 555 123 4567')
                            ->validationMessages([
                                'required' => 'Please enter a phone number',
                            ]),
                        Forms\Components\Select::make('guest.0.country')
                            ->label('Country')
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->options($this->getCountryOptions())
                            ->searchable()
                            ->required()
                            ->placeholder('Select country')
                            ->validationMessages([
                                'required' => 'Please select a country',
                            ]),
                        Forms\Components\DatePicker::make('guest.0.birth_date')
                            ->label('Date of Birth')
                            ->prefixIcon('heroicon-o-cake')
                            ->maxDate(now()->subYears(18))
                            ->displayFormat('d M Y')
                            ->placeholder('Select date'),
                    ]),
            ])
            ->extraAttributes(['class' => 'guest-card']);
            
        // Additional Guests with better styling
        if ($totalGuests > 1) {
            $schema[] = Forms\Components\Section::make('Additional Guests')
                ->description('Please provide details for all travelers')
                ->icon('heroicon-o-user-group')
                ->collapsible()
                ->schema([
                    Forms\Components\Repeater::make('additional_guests')
                        ->schema([
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('first_name')
                                        ->label('First Name')
                                        ->prefixIcon('heroicon-o-user')
                                        ->required()
                                        ->placeholder('First name'),
                                    Forms\Components\TextInput::make('last_name')
                                        ->label('Last Name')
                                        ->prefixIcon('heroicon-o-user')
                                        ->required()
                                        ->placeholder('Last name'),
                                    Forms\Components\Select::make('type')
                                        ->label('Guest Type')
                                        ->prefixIcon('heroicon-o-identification')
                                        ->options([
                                            'adult' => 'Adult',
                                            'child' => 'Child',
                                        ])
                                        ->required()
                                        ->default('adult'),
                                ]),
                        ])
                        ->minItems($totalGuests - 1)
                        ->maxItems($totalGuests - 1)
                        ->defaultItems($totalGuests - 1)
                        ->addActionLabel('Add Guest')
                        ->itemLabel(fn (array $state): ?string => 
                            ($state['first_name'] ?? '') . ' ' . ($state['last_name'] ?? '') ?: 'Guest'
                        ),
                ])
                ->extraAttributes(['class' => 'guest-card']);
        }
        
        // Special Requests & Add-ons with enhanced visuals
        $schema[] = Forms\Components\Section::make('Special Requests & Add-ons')
            ->icon('heroicon-o-sparkles')
            ->schema([
                Forms\Components\Textarea::make('special_requests')
                    ->label('Special Requests')
                    ->placeholder('Any special requests for your stay? (e.g., late check-in, high floor, quiet room, special dietary requirements)')
                    ->rows(3)
                    ->helperText('We\'ll do our best to accommodate your requests'),
                    
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Toggle::make('airport_transfer')
                            ->label('Airport Transfer')
                            ->helperText('Add airport pickup/drop-off service (+₺50)')
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->airportTransfer = $state),
                            
                        Forms\Components\Toggle::make('travel_insurance')
                            ->label('Travel Insurance')
                            ->helperText('Protect your trip with comprehensive coverage (+₺25)')
                            ->live()
                            ->afterStateUpdated(fn ($state) => $this->travelInsurance = $state),
                    ]),
                    
                Forms\Components\TextInput::make('loyalty_number')
                    ->label('Loyalty Program Number')
                    ->prefixIcon('heroicon-o-star')
                    ->placeholder('Enter your membership number')
                    ->helperText('Earn points for this booking'),
            ])
            ->extraAttributes(['class' => 'guest-card']);
            
        return $schema;
    }

    protected function getPaymentSummarySchema(): array
    {
        $hotel = $this->getSelectedHotel();
        $nights = $this->getNights();
        $subtotal = array_sum(array_column($this->selectedRooms, 'total_price'));
        $airportTransferPrice = ($this->data['airport_transfer'] ?? false) ? 50 : 0;
        $travelInsurancePrice = ($this->data['travel_insurance'] ?? false) ? 25 : 0;
        $totalPrice = $subtotal + $airportTransferPrice + $travelInsurancePrice;
        
        // Create comprehensive booking summary HTML
        $summaryHtml = '<div class="summary-section">';
        
        // Hotel & Room Details
        if ($hotel) {
            $summaryHtml .= '<div class="booking-detail-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">';
            $summaryHtml .= '<h3 class="text-lg font-semibold mb-4 flex items-center">';
            $summaryHtml .= '<svg class="w-5 h-5 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
            $summaryHtml .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />';
            $summaryHtml .= '</svg>Accommodation Details</h3>';
            
            $summaryHtml .= '<div class="space-y-3">';
            $summaryHtml .= '<div class="flex items-start">';
            $summaryHtml .= '<div class="flex-1">';
            $summaryHtml .= '<h4 class="font-semibold text-base">' . e($hotel->name) . ' ' . str_repeat('⭐', $hotel->star_rating) . '</h4>';
            $summaryHtml .= '<p class="text-sm text-gray-600 dark:text-gray-400">' . e($hotel->region->name ?? 'Unknown location') . '</p>';
            $summaryHtml .= '</div>';
            $summaryHtml .= '</div>';
            
            // Date and guest information
            $summaryHtml .= '<div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-200 dark:border-gray-700">';
            $summaryHtml .= '<div>';
            $summaryHtml .= '<p class="text-sm text-gray-500 dark:text-gray-400">Check-in</p>';
            $summaryHtml .= '<p class="font-medium">' . Carbon::parse($this->checkIn)->format('D, d M Y') . '</p>';
            $summaryHtml .= '</div>';
            $summaryHtml .= '<div>';
            $summaryHtml .= '<p class="text-sm text-gray-500 dark:text-gray-400">Check-out</p>';
            $summaryHtml .= '<p class="font-medium">' . Carbon::parse($this->checkOut)->format('D, d M Y') . '</p>';
            $summaryHtml .= '</div>';
            $summaryHtml .= '<div>';
            $summaryHtml .= '<p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>';
            $summaryHtml .= '<p class="font-medium">' . $nights . ' ' . ($nights === 1 ? 'Night' : 'Nights') . '</p>';
            $summaryHtml .= '</div>';
            $summaryHtml .= '<div>';
            $summaryHtml .= '<p class="text-sm text-gray-500 dark:text-gray-400">Guests</p>';
            $summaryHtml .= '<p class="font-medium">' . $this->adults . ' ' . ($this->adults === 1 ? 'Adult' : 'Adults');
            if ($this->children > 0) {
                $summaryHtml .= ', ' . $this->children . ' ' . ($this->children === 1 ? 'Child' : 'Children');
            }
            $summaryHtml .= '</p>';
            $summaryHtml .= '</div>';
            $summaryHtml .= '</div>';
            
            // Room details
            $summaryHtml .= '<div class="pt-3 border-t border-gray-200 dark:border-gray-700">';
            $summaryHtml .= '<h5 class="font-medium mb-2">Selected Rooms</h5>';
            foreach ($this->selectedRooms as $room) {
                $summaryHtml .= '<div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 mb-2">';
                $summaryHtml .= '<div class="flex justify-between items-start">';
                $summaryHtml .= '<div>';
                $summaryHtml .= '<p class="font-medium">' . e($room['room_name']) . '</p>';
                $summaryHtml .= '<p class="text-sm text-gray-600 dark:text-gray-400">' . e($room['board_type_name']) . '</p>';
                if ($room['is_per_person'] ?? false) {
                    $summaryHtml .= '<p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Price per person</p>';
                }
                $summaryHtml .= '</div>';
                $summaryHtml .= '<div class="text-right">';
                $summaryHtml .= '<p class="font-semibold">₺' . number_format($room['total_price'], 2) . '</p>';
                $summaryHtml .= '<p class="text-xs text-gray-500">₺' . number_format($room['price_per_night'], 2) . '/night</p>';
                $summaryHtml .= '</div>';
                $summaryHtml .= '</div>';
                $summaryHtml .= '</div>';
            }
            $summaryHtml .= '</div>';
            
            $summaryHtml .= '</div>';
            $summaryHtml .= '</div>';
        }
        
        // Price Breakdown
        $summaryHtml .= '<div class="price-breakdown-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">';
        $summaryHtml .= '<h3 class="text-lg font-semibold mb-4 flex items-center">';
        $summaryHtml .= '<svg class="w-5 h-5 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        $summaryHtml .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />';
        $summaryHtml .= '</svg>Price Breakdown</h3>';
        
        $summaryHtml .= '<div class="space-y-2">';
        
        // Room subtotal
        $summaryHtml .= '<div class="flex justify-between py-2">';
        $summaryHtml .= '<span class="text-gray-600 dark:text-gray-400">Room Total (' . count($this->selectedRooms) . ' ' . (count($this->selectedRooms) === 1 ? 'room' : 'rooms') . ')</span>';
        $summaryHtml .= '<span class="font-medium">₺' . number_format($subtotal, 2) . '</span>';
        $summaryHtml .= '</div>';
        
        // Child pricing details if applicable
        if ($this->children > 0 && !empty($this->childrenAges)) {
            $summaryHtml .= '<div class="text-sm text-gray-500 dark:text-gray-500 italic pl-4">';
            $summaryHtml .= 'Children ages: ' . implode(', ', array_map(fn($age) => $age . ' years', $this->childrenAges));
            $summaryHtml .= '</div>';
        }
        
        // Add-ons
        if ($airportTransferPrice > 0) {
            $summaryHtml .= '<div class="flex justify-between py-2">';
            $summaryHtml .= '<span class="text-gray-600 dark:text-gray-400">Airport Transfer</span>';
            $summaryHtml .= '<span class="font-medium">₺' . number_format($airportTransferPrice, 2) . '</span>';
            $summaryHtml .= '</div>';
        }
        
        if ($travelInsurancePrice > 0) {
            $summaryHtml .= '<div class="flex justify-between py-2">';
            $summaryHtml .= '<span class="text-gray-600 dark:text-gray-400">Travel Insurance</span>';
            $summaryHtml .= '<span class="font-medium">₺' . number_format($travelInsurancePrice, 2) . '</span>';
            $summaryHtml .= '</div>';
        }
        
        // Total
        $summaryHtml .= '<div class="flex justify-between pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">';
        $summaryHtml .= '<span class="text-lg font-semibold">Total Amount</span>';
        $summaryHtml .= '<span class="text-lg font-semibold text-primary-600">₺' . number_format($totalPrice, 2) . '</span>';
        $summaryHtml .= '</div>';
        
        $summaryHtml .= '</div>';
        $summaryHtml .= '</div>';
        
        // Guest Information Summary
        if (!empty($this->data['guest'][0])) {
            $summaryHtml .= '<div class="booking-detail-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">';
            $summaryHtml .= '<h3 class="text-lg font-semibold mb-4 flex items-center">';
            $summaryHtml .= '<svg class="w-5 h-5 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
            $summaryHtml .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />';
            $summaryHtml .= '</svg>Guest Information</h3>';
            
            $summaryHtml .= '<div class="space-y-2">';
            $summaryHtml .= '<p><span class="text-gray-500 dark:text-gray-400">Primary Guest:</span> <span class="font-medium">' . e($this->data['guest'][0]['first_name'] ?? '') . ' ' . e($this->data['guest'][0]['last_name'] ?? '') . '</span></p>';
            $summaryHtml .= '<p><span class="text-gray-500 dark:text-gray-400">Email:</span> <span class="font-medium">' . e($this->data['guest'][0]['email'] ?? '') . '</span></p>';
            $summaryHtml .= '<p><span class="text-gray-500 dark:text-gray-400">Phone:</span> <span class="font-medium">' . e($this->data['guest'][0]['phone'] ?? '') . '</span></p>';
            
            if (!empty($this->data['special_requests'])) {
                $summaryHtml .= '<div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">';
                $summaryHtml .= '<p class="text-gray-500 dark:text-gray-400 mb-1">Special Requests:</p>';
                $summaryHtml .= '<p class="text-sm italic">' . e($this->data['special_requests']) . '</p>';
                $summaryHtml .= '</div>';
            }
            
            $summaryHtml .= '</div>';
            $summaryHtml .= '</div>';
        }
        
        $summaryHtml .= '</div>';
        
        return [
            // Comprehensive Booking Summary
            Forms\Components\Placeholder::make('comprehensive_booking_summary')
                ->label('')
                ->content(new HtmlString($summaryHtml))
                ->columnSpanFull(),
                
            // Payment Method with Visual Selection
            Forms\Components\Section::make('Payment Method')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Forms\Components\Radio::make('payment_method')
                        ->label('How would you like to pay?')
                        ->options([
                            'credit_card' => new HtmlString('<div class="flex items-center"><svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>Credit/Debit Card</div>'),
                            'bank_transfer' => new HtmlString('<div class="flex items-center"><svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>Bank Transfer</div>'),
                            'pay_at_hotel' => new HtmlString('<div class="flex items-center"><svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>Pay at Hotel</div>'),
                        ])
                        ->default('credit_card')
                        ->required()
                        ->live()
                        ->descriptions([
                            'credit_card' => 'Secure payment with instant confirmation',
                            'bank_transfer' => 'Transfer within 24 hours to confirm booking',
                            'pay_at_hotel' => 'No payment required now, pay when you arrive',
                        ]),
                        
                    // Credit Card Fields with better styling
                    Forms\Components\Group::make([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('card_number')
                                    ->label('Card Number')
                                    ->placeholder('1234 5678 9012 3456')
                                    ->prefixIcon('heroicon-o-credit-card')
                                    ->mask('9999 9999 9999 9999')
                                    ->required(),
                                    
                                Forms\Components\TextInput::make('card_holder')
                                    ->label('Cardholder Name')
                                    ->placeholder('As shown on card')
                                    ->prefixIcon('heroicon-o-user')
                                    ->required(),
                            ]),
                            
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('expiry')
                                    ->label('Expiry Date')
                                    ->placeholder('MM/YY')
                                    ->prefixIcon('heroicon-o-calendar')
                                    ->mask('99/99')
                                    ->required(),
                                    
                                Forms\Components\TextInput::make('cvv')
                                    ->label('CVV')
                                    ->placeholder('123')
                                    ->prefixIcon('heroicon-o-lock-closed')
                                    ->mask('999')
                                    ->required()
                                    ->helperText('3-digit code on back of card'),
                                    
                                Forms\Components\Placeholder::make('security_info')
                                    ->label('')
                                    ->content(new HtmlString('<div class="flex items-center text-sm text-gray-500"><svg class="w-4 h-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>Secure SSL Encryption</div>')),
                            ]),
                            
                        // Accepted cards display
                        Forms\Components\Placeholder::make('accepted_cards')
                            ->label('')
                            ->content(new HtmlString('
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 mb-2">We accept:</p>
                                    <div class="flex space-x-3">
                                        <img src="/images/payment/visa.svg" alt="Visa" class="h-8">
                                        <img src="/images/payment/mastercard.svg" alt="Mastercard" class="h-8">
                                        <img src="/images/payment/paypal.svg" alt="PayPal" class="h-8">
                                        <img src="/images/payment/apple-pay.svg" alt="Apple Pay" class="h-8">
                                    </div>
                                </div>
                            ')),
                    ])
                    ->visible(fn (Get $get) => $get('payment_method') === 'credit_card'),
                    
                    // Bank Transfer Info
                    Forms\Components\Placeholder::make('bank_transfer_info')
                        ->label('')
                        ->content(new HtmlString('
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mt-4">
                                <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Bank Transfer Instructions</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">After completing your booking, you\'ll receive bank details via email. Please transfer the total amount within 24 hours to confirm your reservation.</p>
                                <div class="bg-white dark:bg-gray-800 rounded p-3 space-y-1">
                                    <p class="text-sm"><span class="font-medium">Bank:</span> Tourism Bank</p>
                                    <p class="text-sm"><span class="font-medium">Account Name:</span> TMS Tourism Ltd.</p>
                                    <p class="text-sm"><span class="font-medium">Reference:</span> Will be provided after booking</p>
                                </div>
                            </div>
                        '))
                        ->visible(fn (Get $get) => $get('payment_method') === 'bank_transfer'),
                        
                    // Pay at Hotel Info
                    Forms\Components\Placeholder::make('pay_at_hotel_info')
                        ->label('')
                        ->content(new HtmlString('
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mt-4">
                                <h4 class="font-medium text-green-900 dark:text-green-100 mb-2">Pay at Hotel</h4>
                                <p class="text-sm text-green-700 dark:text-green-300">No payment required now! Your booking will be confirmed immediately and you can pay directly at the hotel during check-in.</p>
                                <ul class="mt-3 space-y-1 text-sm text-green-600 dark:text-green-400">
                                    <li class="flex items-start"><svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Free cancellation until 24 hours before check-in</li>
                                    <li class="flex items-start"><svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Pay with cash or card at the hotel</li>
                                    <li class="flex items-start"><svg class="w-4 h-4 mr-1 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Instant booking confirmation</li>
                                </ul>
                            </div>
                        '))
                        ->visible(fn (Get $get) => $get('payment_method') === 'pay_at_hotel'),
                ]),
                
            // Terms & Conditions with Cancellation Policy
            Forms\Components\Section::make()
                ->schema([
                    // Cancellation Policy
                    Forms\Components\Placeholder::make('cancellation_policy')
                        ->label('')
                        ->content(function() use ($hotel) {
                            $html = '<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">';
                            $html .= '<h4 class="font-medium text-amber-900 dark:text-amber-100 mb-2 flex items-center">';
                            $html .= '<svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>';
                            $html .= 'Cancellation Policy</h4>';
                            
                            if ($hotel && $hotel->refund_policy_type) {
                                $html .= '<div class="text-sm text-amber-700 dark:text-amber-300 space-y-2">';
                                
                                switch($hotel->refund_policy_type) {
                                    case 'flexible':
                                        $html .= '<p>✓ Free cancellation up to 24 hours before check-in</p>';
                                        $html .= '<p>✓ Full refund if cancelled within the free cancellation period</p>';
                                        break;
                                    case 'moderate':
                                        $html .= '<p>⚠ Free cancellation up to 7 days before check-in</p>';
                                        $html .= '<p>⚠ 50% refund if cancelled 3-7 days before check-in</p>';
                                        $html .= '<p>⚠ No refund if cancelled less than 3 days before check-in</p>';
                                        break;
                                    case 'strict':
                                        $html .= '<p>❌ Free cancellation up to 14 days before check-in</p>';
                                        $html .= '<p>❌ 50% refund if cancelled 7-14 days before check-in</p>';
                                        $html .= '<p>❌ No refund if cancelled less than 7 days before check-in</p>';
                                        break;
                                    case 'non_refundable':
                                        $html .= '<p>❌ This is a non-refundable booking</p>';
                                        $html .= '<p>❌ No refund will be provided for cancellations</p>';
                                        $html .= '<p class="font-medium">💰 Save ' . ($hotel->non_refundable_discount ?? 10) . '% with this non-refundable rate</p>';
                                        break;
                                }
                                
                                $html .= '</div>';
                            } else {
                                $html .= '<p class="text-sm text-amber-700 dark:text-amber-300">Standard cancellation policy applies. Please contact the hotel for details.</p>';
                            }
                            
                            $html .= '</div>';
                            
                            return new HtmlString($html);
                        }),
                        
                    Forms\Components\Checkbox::make('accept_terms')
                        ->label('I accept the terms and conditions and cancellation policy')
                        ->required()
                        ->accepted()
                        ->validationMessages([
                            'accepted' => 'You must accept the terms and conditions to proceed',
                        ]),
                ]),
        ];
    }

    // Helper Methods
    
    #[Computed]
    public function getNights(): int
    {
        if (!$this->checkIn || !$this->checkOut) {
            return 0;
        }
        
        return Carbon::parse($this->checkIn)->diffInDays(Carbon::parse($this->checkOut));
    }

    // Removed getDestinationSuggestions method - now using searchable Select field

    protected function getAvailableHotels()
    {
        // Form state'ten destination'ı al
        $formDestination = data_get($this->data, 'destination');
        
        // Session'dan destination'ı al
        $sessionDestination = session('booking_destination');
        
        // Öncelik sırası: form -> session -> property
        $actualDestination = $formDestination ?? $sessionDestination ?? $this->destination;
        
        \Log::info('BookingWizard - Getting Hotels Debug', [
            'this->destination' => $this->destination,
            'form_destination' => $formDestination,
            'session_destination' => $sessionDestination,
            'actual_destination' => $actualDestination,
            'all_data' => $this->data,
        ]);
        
        $query = Hotel::query()
            ->with([
                'rooms' => function($q) {
                    // Sadece satışa açık odaları getir
                    $q->whereHas('ratePlans.dailyRates', function($dq) {
                        $dq->whereBetween('date', [$this->checkIn, $this->checkOut])
                           ->where('is_closed', false)
                           ->whereIn('sales_type', ['direct', 'ask_sell']);
                    });
                },
                'rooms.ratePlans.boardType', 
                'rooms.ratePlans.dailyRates' => function($q) {
                    $q->whereBetween('date', [$this->checkIn, $this->checkOut])
                      ->where('is_closed', false);
                },
                'rooms.amenities', 
                'amenities', 
                'region'
            ])
            // Sadece müsait odası olan otelleri getir
            ->whereHas('rooms.ratePlans.dailyRates', function($q) {
                $q->whereBetween('date', [$this->checkIn, $this->checkOut])
                  ->where('is_closed', false)
                  ->whereIn('sales_type', ['direct', 'ask_sell']);
            });
            
        // Destination filter - artık hiyerarşik arama yapıyor
        \Log::info('BookingWizard - Starting destination filter', [
            'destination' => $actualDestination,
            'destination_type' => gettype($actualDestination),
            'is_numeric' => is_numeric($actualDestination),
        ]);
        
        if ($actualDestination && $actualDestination !== '0') {
            // Önce destination'ın bir region ID olup olmadığını kontrol et
            $destinationRegion = null;
            
            // Eğer numeric ise ID olarak ara
            if (is_numeric($actualDestination)) {
                $destinationRegion = Region::find($actualDestination);
                \Log::info('BookingWizard - Searching by ID', [
                    'destination_id' => $actualDestination,
                    'region_found' => $destinationRegion ? true : false,
                    'region_name' => $destinationRegion ? $destinationRegion->name : 'NOT FOUND',
                ]);
            } else {
                // String ise isimle ara
                $destinationRegion = Region::where('name', 'like', "%{$actualDestination}%")->first();
                \Log::info('BookingWizard - Searching by name', [
                    'destination_name' => $actualDestination,
                    'region_found' => $destinationRegion ? true : false,
                    'region_name' => $destinationRegion ? $destinationRegion->name : 'NOT FOUND',
                ]);
            }
            
            if ($destinationRegion) {
                // Eğer bir region bulunduysa, o region ve tüm alt regionlarındaki otelleri getir
                $regionIds = array_merge(
                    [$destinationRegion->id], 
                    $destinationRegion->getAllChildrenIdsAttribute()
                );
                
                \Log::info('BookingWizard - Region Search Applied', [
                    'destination' => $actualDestination,
                    'region_name' => $destinationRegion->name,
                    'region_id' => $destinationRegion->id,
                    'all_region_ids' => $regionIds,
                    'region_count' => count($regionIds),
                ]);
                
                $query->whereIn('region_id', $regionIds);
                
                // Log the SQL query
                \Log::info('BookingWizard - SQL Query', [
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings(),
                ]);
            } else {
                // Eğer region bulunamadıysa, otel adında ara (fallback)
                \Log::info('BookingWizard - Hotel Name Search (Fallback)', [
                    'destination' => $actualDestination,
                    'search_term' => "%{$actualDestination}%",
                ]);
                
                $query->where('name', 'like', "%{$actualDestination}%");
            }
        } else {
            \Log::info('BookingWizard - No destination filter applied', [
                'this->destination' => $this->destination,
                'actualDestination' => $actualDestination,
                'formDestination' => $formDestination,
            ]);
        }
        
        // Price filter
        if ($this->priceRange[0] > 0 || $this->priceRange[1] < 10000) {
            $query->whereHas('rooms.ratePlans.dailyRates', function ($q) {
                $q->where('date', '>=', $this->checkIn)
                  ->where('date', '<=', $this->checkOut)
                  ->whereBetween('base_price', $this->priceRange);
            });
        }
        
        // Star rating filter
        if (!empty($this->starRatings)) {
            $query->whereIn('star_rating', $this->starRatings);
        }
        
        // Amenities filter
        if (!empty($this->amenities)) {
            $query->whereHas('amenities', function ($q) {
                $q->whereIn('hotel_amenity_id', $this->amenities);
            });
        }
        
        // Sorting
        switch ($this->sortBy) {
            case 'price_low':
                $query->orderBy('min_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('min_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('star_rating', 'desc');
                break;
            default:
                // Recommended sorting logic
                $query->orderBy('star_rating', 'desc')
                      ->orderBy('created_at', 'desc');
        }
        
        // Get total count before pagination
        $totalCount = $query->count();
        
        // Get paginated results
        $hotels = $query->paginate(10);
        
        \Log::info('BookingWizard - Final Results', [
            'total_hotels_found' => $totalCount,
            'current_page' => $hotels->currentPage(),
            'per_page' => $hotels->perPage(),
            'hotel_names' => $hotels->pluck('name')->toArray(),
            'hotel_regions' => $hotels->map(function($hotel) {
                return [
                    'hotel' => $hotel->name,
                    'region_id' => $hotel->region_id,
                    'region_name' => $hotel->region ? $hotel->region->name : 'NO REGION',
                ];
            })->toArray(),
        ]);
        
        return $hotels;
    }

    protected function getCountryOptions(): array
    {
        return [
            'TR' => 'Turkey',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'RU' => 'Russia',
            'CN' => 'China',
            'JP' => 'Japan',
            // Add more countries as needed
        ];
    }

    protected function getSelectedHotel()
    {
        if (!$this->selectedHotelId) {
            return null;
        }
        
        return Hotel::find($this->selectedHotelId);
    }

    protected function calculateTotalPrice(): float
    {
        $total = 0;
        
        foreach ($this->selectedRooms as $room) {
            $total += $room['total_price'] ?? 0;
        }
        
        // Add extras
        if ($this->data['airport_transfer'] ?? false) {
            $total += 50; // Fixed price for now
        }
        
        if ($this->data['travel_insurance'] ?? false) {
            $total += 25; // Fixed price for now
        }
        
        return $total;
    }
    
    /**
     * Calculate room price with child policy
     */
    public function calculateRoomPrice($ratePlan, $dailyRates): array
    {
        $childPricingService = app(ChildPolicyPricingService::class);
        $nights = $this->getNights();
        $totalPrice = 0;
        $priceBreakdown = [];
        
        foreach ($dailyRates as $dailyRate) {
            $dayPrice = $childPricingService->calculatePriceWithChildren(
                $ratePlan,
                $dailyRate,
                $this->adults,
                $this->childrenAges,
                1 // Single night calculation
            );
            
            $totalPrice += $dayPrice['price_per_night'];
            $priceBreakdown[] = [
                'date' => $dailyRate->date->format('Y-m-d'),
                'price' => $dayPrice['price_per_night'],
                'adults_price' => $dayPrice['adults_price'],
                'children_price' => $dayPrice['children_price'],
                'free_children' => $dayPrice['free_children'],
                'paid_children' => $dayPrice['paid_children'],
            ];
        }
        
        return [
            'total_price' => $totalPrice,
            'price_per_night' => $nights > 0 ? ($totalPrice / $nights) : 0,
            'nights' => $nights,
            'breakdown' => $priceBreakdown,
            'currency' => $dailyRates->first()->currency ?? 'TRY',
        ];
    }
    
    /**
     * Calculate price for rate plan (for use in views)
     */
    public function getPriceForRatePlan($ratePlan, $dailyRates)
    {
        if ($dailyRates->isEmpty()) {
            return [
                'average_price' => 0,
                'total_price' => 0,
                'has_children_discount' => false,
                'children_info' => null,
            ];
        }
        
        $priceData = $this->calculateRoomPrice($ratePlan, $dailyRates);
        $hasChildrenDiscount = false;
        $childrenInfo = null;
        
        if (count($this->childrenAges) > 0) {
            $freeChildren = 0;
            $paidChildren = 0;
            
            foreach ($priceData['breakdown'] as $day) {
                $freeChildren = max($freeChildren, $day['free_children']);
                $paidChildren = max($paidChildren, $day['paid_children']);
            }
            
            if ($freeChildren > 0 || $paidChildren > 0) {
                $hasChildrenDiscount = true;
                $childrenInfo = [
                    'free' => $freeChildren,
                    'paid' => $paidChildren,
                    'discounted' => $paidChildren, // paid children are discounted children
                    'total' => count($this->childrenAges),
                ];
            }
        }
        
        return [
            'average_price' => $priceData['price_per_night'],
            'total_price' => $priceData['total_price'],
            'has_children_discount' => $hasChildrenDiscount,
            'children_info' => $childrenInfo,
            'currency' => $priceData['currency'],
        ];
    }

    // Computed property for available hotels
    #[Computed]
    public function availableHotels()
    {
        return $this->getAvailableHotels();
    }
    
    // Livewire Methods
    
    public function selectHotel($hotelId): void
    {
        $this->selectedHotelId = $hotelId;
        $this->selectedRooms = [];
        
        $this->dispatch('hotel-selected', hotelId: $hotelId);
    }

    public function addRoom($roomData): void
    {
        $roomId = $roomData['room_id'];
        $ratePlanId = $roomData['rate_plan_id'];
        
        // Use the total price that was already calculated in the view
        $totalPrice = $roomData['total_price'] ?? 0;
        $pricePerNight = $roomData['price_per_night'] ?? 0;
        $nights = $this->getNights();
        
        $this->selectedRooms[] = [
            'room_id' => $roomId,
            'rate_plan_id' => $ratePlanId,
            'room_name' => $roomData['room_name'],
            'board_type' => $roomData['board_type'],
            'board_type_name' => $roomData['board_type_name'] ?? '',
            'price_per_night' => $pricePerNight,
            'total_price' => $totalPrice,
            'nights' => $nights,
            'is_per_person' => $roomData['is_per_person'] ?? false,
        ];
        
        Notification::make()
            ->title('Room added')
            ->body($roomData['room_name'] . ' with ' . $roomData['board_type_name'] . ' has been added to your booking.')
            ->success()
            ->send();
    }

    public function removeRoom($index): void
    {
        unset($this->selectedRooms[$index]);
        $this->selectedRooms = array_values($this->selectedRooms);
        
        Notification::make()
            ->title('Room removed')
            ->success()
            ->send();
    }

    public function updateFilters($filters): void
    {
        $this->priceRange = $filters['priceRange'] ?? $this->priceRange;
        $this->starRatings = $filters['starRatings'] ?? $this->starRatings;
        $this->boardTypes = $filters['boardTypes'] ?? $this->boardTypes;
        $this->amenities = $filters['amenities'] ?? $this->amenities;
        $this->sortBy = $filters['sortBy'] ?? $this->sortBy;
    }
    
    public function handleSaleOnRequest($roomData): void
    {
        // SOR (Sale on Request) odalar için özel işlem
        Notification::make()
            ->title('Sale on Request')
            ->body('This room is available on request. Please contact us at +90 123 456 7890 or email info@hotel.com for availability confirmation.')
            ->warning()
            ->duration(10000)
            ->send();
            
        // İsteğe bağlı: SOR talebini veritabanına kaydet
        \Log::info('Sale on Request initiated', [
            'room_id' => $roomData['room_id'] ?? null,
            'user' => auth()->user()?->email,
            'date' => now()
        ]);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        
        DB::beginTransaction();
        
        try {
            // Create reservation for each room
            foreach ($this->selectedRooms as $room) {
                $reservation = Reservation::create([
                    'user_id' => auth()->id(),
                    'hotel_id' => $this->selectedHotelId,
                    'room_id' => $room['room_id'],
                    'board_type_id' => BoardType::where('code', $room['board_type'])->first()->id,
                    'check_in' => $this->checkIn,
                    'check_out' => $this->checkOut,
                    'adults' => $this->adults,
                    'children' => $this->children,
                    'children_ages' => $this->childrenAges,
                    'total_price' => $room['total_price'],
                    'status' => $data['payment_method'] === 'pay_at_hotel' ? 'confirmed' : 'pending',
                    'payment_method' => $data['payment_method'],
                    'special_requests' => $data['special_requests'] ?? null,
                    'confirmation_code' => strtoupper(uniqid('TMS')),
                ]);
                
                // Create primary guest
                Guest::create([
                    'reservation_id' => $reservation->id,
                    'first_name' => $data['guest'][0]['first_name'],
                    'last_name' => $data['guest'][0]['last_name'],
                    'email' => $data['guest'][0]['email'],
                    'phone' => $data['guest'][0]['phone'],
                    'country' => $data['guest'][0]['country'],
                    'birth_date' => $data['guest'][0]['birth_date'] ?? null,
                    'is_primary' => true,
                ]);
                
                // Create additional guests
                if (isset($data['additional_guests'])) {
                    foreach ($data['additional_guests'] as $guest) {
                        Guest::create([
                            'reservation_id' => $reservation->id,
                            'first_name' => $guest['first_name'],
                            'last_name' => $guest['last_name'],
                            'type' => $guest['type'],
                            'is_primary' => false,
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            Notification::make()
                ->title('Booking created successfully!')
                ->success()
                ->send();
                
            $this->redirect(ReservationResource::getUrl('index'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title('Error creating booking')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}