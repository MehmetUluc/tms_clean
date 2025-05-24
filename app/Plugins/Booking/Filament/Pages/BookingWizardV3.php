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
                ->submitAction('Complete Booking')
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
                                    ->content(fn () => view('booking::components.booking-wizard.no-children-placeholder'))
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
                                ->content(fn () => view('booking::components.booking-wizard.booking-summary', [
                                    'nights' => $this->getNights(),
                                    'totalGuests' => $this->adults + $this->children,
                                ]))
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
            $schema[] = Forms\Components\Placeholder::make('selection_summary')
                ->label('')
                ->content(fn () => view('booking::components.booking-wizard.selection-summary', [
                    'hotel' => $this->getSelectedHotel(),
                    'selectedRooms' => $this->selectedRooms,
                    'checkIn' => $this->checkIn,
                    'checkOut' => $this->checkOut,
                    'nights' => $this->getNights(),
                    'adults' => $this->adults,
                    'children' => $this->children,
                ]));
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
        $taxes = $subtotal * 0.18; // %18 KDV
        $discount = 0; // TODO: Implement discount logic
        $totalPrice = $subtotal + $airportTransferPrice + $travelInsurancePrice + $taxes - $discount;
        
        return [
            // Comprehensive Booking Summary
            Forms\Components\Placeholder::make('comprehensive_booking_summary')
                ->label('')
                ->content(fn () => view('booking::components.booking-wizard.payment-summary', [
                    'hotel' => $hotel,
                    'selectedRooms' => $this->selectedRooms,
                    'checkIn' => $this->checkIn,
                    'checkOut' => $this->checkOut,
                    'nights' => $nights,
                    'adults' => $this->adults,
                    'children' => $this->children,
                    'childrenAges' => $this->childrenAges,
                    'subtotal' => $subtotal,
                    'airportTransferPrice' => $airportTransferPrice,
                    'travelInsurancePrice' => $travelInsurancePrice,
                    'discount' => $discount,
                    'taxes' => $taxes,
                    'totalPrice' => $totalPrice,
                    'guest' => $this->data['guest'][0] ?? [],
                    'specialRequests' => $this->data['special_requests'] ?? null,
                ]))
                ->columnSpanFull(),
                
            // Payment Method with Visual Selection
            Forms\Components\Section::make('Payment Method')
                ->icon('heroicon-o-credit-card')
                ->schema([
                    Forms\Components\Radio::make('payment_method')
                        ->label('How would you like to pay?')
                        ->options([
                            'credit_card' => 'Credit/Debit Card',
                            'bank_transfer' => 'Bank Transfer',
                            'pay_at_hotel' => 'Pay at Hotel',
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
                                    ->content(fn () => view('booking::components.booking-wizard.security-info')),
                            ]),
                            
                        // Accepted cards display
                        Forms\Components\Placeholder::make('accepted_cards')
                            ->label('')
                            ->content(fn () => view('booking::components.booking-wizard.accepted-cards')),
                    ])
                    ->visible(fn (Get $get) => $get('payment_method') === 'credit_card'),
                    
                    // Bank Transfer Info
                    Forms\Components\Placeholder::make('bank_transfer_info')
                        ->label('')
                        ->content(fn () => view('booking::components.booking-wizard.bank-transfer-info'))
                        ->visible(fn (Get $get) => $get('payment_method') === 'bank_transfer'),
                        
                    // Pay at Hotel Info
                    Forms\Components\Placeholder::make('pay_at_hotel_info')
                        ->label('')
                        ->content(fn () => view('booking::components.booking-wizard.pay-at-hotel-info'))
                        ->visible(fn (Get $get) => $get('payment_method') === 'pay_at_hotel'),
                ]),
                
            // Terms & Conditions with Cancellation Policy
            Forms\Components\Section::make()
                ->schema([
                    // Cancellation Policy
                    Forms\Components\Placeholder::make('cancellation_policy')
                        ->label('')
                        ->content(fn () => view('booking::components.booking-wizard.cancellation-policy', [
                            'hotel' => $hotel
                        ])),
                        
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