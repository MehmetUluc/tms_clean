<?php

namespace App\Plugins\Pricing\Filament\Livewire;

use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Pricing\Models\RateException;
use App\Plugins\Pricing\Models\RatePeriod;
use App\Plugins\Pricing\Models\RatePlan;
use App\Plugins\Pricing\Services\PricingService;
use App\Plugins\Pricing\Services\DailyRateService;
use App\Plugins\Pricing\Repositories\RateExceptionRepository;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class PricingForm extends Component implements HasForms
{
    use InteractsWithForms;

    public array $ratePlans = [];
    public array $roomsData = [];
    public array $boardTypesData = [];
    public array $pricingData = [];
    public array $dateRange = [];

    // Hotel data including refund policy
    public array $hotelData = [];

    // Form data for each rate plan
    public array $formData = [];

    // Control values for bulk updates
    public array $controls = [];

    private PricingService $pricingService;
    private RateExceptionRepository $rateExceptionRepository;

    public function boot(): void
    {
        $this->pricingService = app(PricingService::class);
        $this->rateExceptionRepository = app(RateExceptionRepository::class);
    }

    public function mount(): void
    {
        // Initialize controls array
        $this->initializeControls();
    }

    private function initializeControls(): void
    {
        $this->controls = [];

        foreach ($this->ratePlans as $ratePlanId => $ratePlan) {
            $isPerPerson = $ratePlan['is_per_person'] ?? false;
            $room = $this->roomsData[$ratePlan['room_id']] ?? null;

            $this->controls[$ratePlanId] = [
                'base_price' => null,
                'min_stay' => 1,
                'quantity' => 1,
                'sales_type' => 'direct',
                'ask_sell' => false, // Default value for Sor-Sat toggle (off)
                'status' => true,    // Default value for Durum toggle (on)
            ];

            if ($isPerPerson && $room) {
                $maxOccupancy = $room['capacity'] ?? 3;

                for ($i = 1; $i <= $maxOccupancy; $i++) {
                    $this->controls[$ratePlanId]["price_{$i}"] = null;
                }
            }
        }
    }

    #[On('pricingDataReady')]
    public function loadPricingData($data): void
    {
        try {
            // Trigger a short delay using JavaScript to ensure UI is responsive
            $this->dispatch('pricing-form-loading');

            // Received pricing data

            // Check if hotel refund policy is available

            // More thorough validation of incoming data - only require rate plans and date range
            if (empty($data['ratePlans'])) {
                Log::error('No rate plans data received');
                Notification::make()
                    ->title('Fiyat verileri yüklenemedi')
                    ->body('Fiyat planları bulunamadı. Lütfen oda ve pansiyon tipi seçimi yapınız.')
                    ->danger()
                    ->send();
                $this->dispatch('pricing-form-error', ['message' => 'Fiyat planları bulunamadı']);
                return;
            }

            if (empty($data['dateRange'])) {
                Log::error('No date range data received');
                Notification::make()
                    ->title('Fiyat verileri yüklenemedi')
                    ->body('Tarih aralığı bulunamadı. Lütfen tarih aralığı seçimi yapınız.')
                    ->danger()
                    ->send();
                $this->dispatch('pricing-form-error', ['message' => 'Tarih aralığı bulunamadı']);
                return;
            }

            // Continue if rooms or board types data is missing

            // Set all the data properties
            $ratePlans = $data['ratePlans'] ?? [];
            $this->ratePlans = $ratePlans;
            $this->roomsData = $data['roomsData'] ?? [];
            $this->boardTypesData = $data['boardTypesData'] ?? [];
            $this->pricingData = $data['pricingData'] ?? [];
            $this->dateRange = $data['dateRange'] ?? [];
            $this->hotelData = $data['hotelData'] ?? [];

            // Check rate plans structure

            // Process each rate plan to ensure all required attributes are set
            foreach ($this->ratePlans as $ratePlanId => &$ratePlan) {
                // Make sure room_id exists and try to find room data if available
                if (!isset($ratePlan['room_id'])) {
                    Log::warning("Missing room_id for rate plan: " . $ratePlanId);
                    $ratePlan['room_name'] = 'Oda (ID bulunamadı)';
                } else {
                    $roomId = $ratePlan['room_id'];

                    // First check if we have room data in roomsData
                    if (!empty($this->roomsData) && isset($this->roomsData[$roomId])) {
                        $roomData = $this->roomsData[$roomId];
                        $ratePlan['room_name'] = $roomData['name'] ?? 'Oda';
                    } else {
                        // If not in roomsData, try to load from database directly
                        try {
                            $room = Room::find($roomId);
                            if ($room) {
                                $ratePlan['room_name'] = $room->name;
                                // Add to roomsData for future use
                                $this->roomsData[$roomId] = $room->toArray();
                            } else {
                                $ratePlan['room_name'] = 'Oda (ID: ' . $roomId . ')';
                            }
                        } catch (\Exception $e) {
                            Log::warning("Failed to load room for rate plan: " . $e->getMessage());
                            $ratePlan['room_name'] = 'Oda (ID: ' . $roomId . ')';
                        }
                    }
                }

                // Make sure board_type_id exists and try to find board type data if available
                if (!isset($ratePlan['board_type_id'])) {
                    Log::warning("Missing board_type_id for rate plan: " . $ratePlanId);
                    $ratePlan['board_type_name'] = 'Pansiyon (ID bulunamadı)';
                } else {
                    $boardTypeId = $ratePlan['board_type_id'];

                    // First check if we have board type data in boardTypesData
                    if (!empty($this->boardTypesData) && isset($this->boardTypesData[$boardTypeId])) {
                        $boardTypeData = $this->boardTypesData[$boardTypeId];
                        $ratePlan['board_type_name'] = $boardTypeData['name'] ?? 'Pansiyon Tipi';
                    } else {
                        // If not in boardTypesData, try to load from database directly
                        try {
                            $boardType = BoardType::find($boardTypeId);
                            if ($boardType) {
                                $ratePlan['board_type_name'] = $boardType->name;
                                // Add to boardTypesData for future use
                                $this->boardTypesData[$boardTypeId] = $boardType->toArray();
                            } else {
                                $ratePlan['board_type_name'] = 'Pansiyon (ID: ' . $boardTypeId . ')';
                            }
                        } catch (\Exception $e) {
                            Log::warning("Failed to load board type for rate plan: " . $e->getMessage());
                            $ratePlan['board_type_name'] = 'Pansiyon (ID: ' . $boardTypeId . ')';
                        }
                    }
                }

                // Ensure is_per_person is set
                if (!isset($ratePlan['is_per_person'])) {
                    Log::warning("is_per_person not set for rate plan, defaulting to false: " . $ratePlanId);
                    $ratePlan['is_per_person'] = false;
                }
            }

            // Initialize form data from pricing data
            $this->initializeFormData();

            // Initialize controls for bulk updates
            $this->initializeControls();

            // Pricing form initialized successfully

            // Notify success with form loaded event
            Notification::make()
                ->title('Fiyat verileri yüklendi')
                ->body('Fiyat tablosu başarıyla hazırlandı.')
                ->success()
                ->send();

            // Dispatch a success event to signal parent component form is fully loaded
            $this->dispatch('pricing-form-loaded');

            // Force render the component to ensure UI is updated
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            \Log::error('Error loading pricing data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title('Fiyat verileri yüklenemedi')
                ->body('Hata: ' . $e->getMessage())
                ->danger()
                ->send();

            // Dispatch error event to signal parent component
            $this->dispatch('pricing-form-error', [
                'message' => $e->getMessage()
            ]);
        }
    }

    private function initializeFormData(): void
    {
        $this->formData = [];

        foreach ($this->ratePlans as $ratePlanId => $ratePlan) {
            $this->formData[$ratePlanId] = [];
            $isPerPerson = $ratePlan['is_per_person'] ?? false;
            $pricingMethod = $ratePlan['pricing_calculation_method'] ?? ($isPerPerson ? 'per_person' : 'per_room');
            $capacity = $ratePlan['capacity'] ?? 2;

            // Get room data - may be needed for capacity and other properties
            $roomId = $ratePlan['room_id'] ?? null;
            $room = $roomId && isset($this->roomsData[$roomId]) ? $this->roomsData[$roomId] : null;

            // If room data available but capacity not set in rate plan, use room capacity
            if ($room && !isset($ratePlan['capacity'])) {
                $capacity = $room['capacity_adults'] ?? 2;
            }

            // Get pricing data for this rate plan
            $planPricing = $this->pricingData[$ratePlanId] ?? [];

            // Get refund policy info
            $refundPolicy = !empty($this->hotelData['refund_policy']) ? $this->hotelData['refund_policy'] : [
                'allow_refundable' => true,
                'allow_non_refundable' => true,
                'non_refundable_discount' => 0,
            ];

            \Log::debug('Initializing form data for rate plan', [
                'ratePlanId' => $ratePlanId,
                'isPerPerson' => $isPerPerson,
                'pricingMethod' => $pricingMethod,
                'capacity' => $capacity,
                'roomId' => $roomId,
                'refundPolicy' => $refundPolicy,
            ]);

            foreach ($this->dateRange as $date) {
                $dayPricing = $planPricing[$date] ?? [
                    'has_pricing' => false,
                    'base_price' => 0, // Default to 0 instead of null
                    'prices' => null,
                    'min_stay' => 1,
                    'quantity' => 1,
                    'sales_type' => 'direct',
                    'status' => true,
                    'is_exception' => false,
                    'is_refundable' => true, // Default to refundable
                ];

                // Ensure base_price is not null
                $basePrice = $dayPricing['base_price'];
                if ($basePrice === null) {
                    $basePrice = 0;
                }

                // Important: When is_per_person is true, set prices array correctly
                $prices = $dayPricing['prices'] ?? [];

                // Check if we have prices_json data from database
                if ($isPerPerson && empty($prices) && isset($dayPricing['prices_json']) && $dayPricing['prices_json']) {
                    // Convert JSON string to array or get the array directly if already decoded
                    if (is_string($dayPricing['prices_json'])) {
                        $prices = json_decode($dayPricing['prices_json'], true);
                    } else if (is_array($dayPricing['prices_json'])) {
                        $prices = $dayPricing['prices_json'];
                    }
                    \Log::debug('PricingForm: Loaded prices from prices_json field', [
                        'ratePlanId' => $ratePlanId,
                        'date' => $date,
                        'prices_json' => $dayPricing['prices_json'],
                        'decoded_prices' => $prices
                    ]);
                }

                // If is_per_person is true but prices is empty or not an array,
                // create a prices array with the base price as the single person price
                if ($isPerPerson && (empty($prices) || !is_array($prices))) {
                    $prices = [];

                    // Initialize prices for all possible occupancy values (1 to capacity)
                    for ($i = 1; $i <= $capacity; $i++) {
                        // If it's the first person, use base price
                        // For additional persons, use a calculated value (e.g., 80% of base price per additional person)
                        if ($i === 1) {
                            $prices[(string)$i] = $basePrice;
                        } else {
                            // Default calculation: additional persons at 80% of base price
                            $prices[(string)$i] = round($basePrice * 0.8, 2);
                        }
                    }

                    \Log::debug('Created prices array for is_per_person rate plan', [
                        'ratePlanId' => $ratePlanId,
                        'date' => $date,
                        'base_price' => $basePrice,
                        'created_prices' => $prices,
                        'capacity' => $capacity,
                    ]);
                }

                // Set up refundable options
                $refundOptions = [
                    'is_refundable' => $dayPricing['is_refundable'] ?? true,
                    'non_refundable_price' => isset($basePrice) ? round($basePrice * (1 - ($refundPolicy['non_refundable_discount'] / 100)), 2) : 0,
                ];

                // Check if sales_type is ask_sell to set the toggle correctly
                $salesType = $dayPricing['sales_type'] ?? 'direct';
                $isAskSell = ($salesType === 'ask_sell');

                $this->formData[$ratePlanId][$date] = [
                    'base_price' => $basePrice,
                    'prices' => $prices,
                    'min_stay' => $dayPricing['min_stay'] ?? 1,
                    'quantity' => $dayPricing['quantity'] ?? 1,
                    'sales_type' => $salesType,
                    'ask_sell' => $isAskSell, // Add ask_sell field for toggle
                    'status' => $dayPricing['status'] ?? true, // Default status to true
                    'is_exception' => $dayPricing['is_exception'] ?? false,
                    'exception_id' => $dayPricing['exception_id'] ?? null,
                    'is_per_person' => $isPerPerson,
                    'pricing_method' => $pricingMethod,
                    'capacity' => $capacity,
                    'refund_options' => $refundOptions,
                    'original_data' => [ // Track original values to detect changes
                        'base_price' => $basePrice,
                        'prices' => $prices,
                        'min_stay' => $dayPricing['min_stay'] ?? 1,
                        'quantity' => $dayPricing['quantity'] ?? 1,
                        'sales_type' => $salesType,
                        'ask_sell' => $isAskSell,
                        'status' => $dayPricing['status'] ?? true,
                        'is_refundable' => $dayPricing['is_refundable'] ?? true,
                    ],
                ];
            }
        }
    }

    // Apply control values to all dates for a rate plan
    public function applyControl($ratePlanId, $field): void
    {
        $value = $this->controls[$ratePlanId][$field] ?? null;

        if ($value === null) {
            return;
        }

        $isPerPerson = $this->ratePlans[$ratePlanId]['is_per_person'] ?? false;

        foreach ($this->dateRange as $date) {
            if ($field === 'base_price') {
                $this->formData[$ratePlanId][$date]['base_price'] = $value;

                // For per-person pricing, also update the prices[1] value
                if ($isPerPerson) {
                    $prices = $this->formData[$ratePlanId][$date]['prices'] ?? [];
                    $prices['1'] = $value;
                    $this->formData[$ratePlanId][$date]['prices'] = $prices;

                    \Log::debug('Updated prices array when base_price changed for per-person rate plan', [
                        'ratePlanId' => $ratePlanId,
                        'date' => $date,
                        'base_price' => $value,
                        'updated_prices' => $prices,
                    ]);
                }
            } elseif (str_starts_with($field, 'price_')) {
                $personCount = substr($field, 6);
                $prices = $this->formData[$ratePlanId][$date]['prices'] ?? [];
                $prices[$personCount] = $value;
                $this->formData[$ratePlanId][$date]['prices'] = $prices;

                // If this is the single person price (prices[1]), also update base_price
                if ($personCount === '1' && $isPerPerson) {
                    $this->formData[$ratePlanId][$date]['base_price'] = $value;

                    \Log::debug('Updated base_price when prices[1] changed for per-person rate plan', [
                        'ratePlanId' => $ratePlanId,
                        'date' => $date,
                        'prices_1' => $value,
                        'updated_base_price' => $value,
                    ]);
                }
            } elseif ($field === 'ask_sell') {
                // Handle the ask_sell toggle (maps to sales_type)
                $this->formData[$ratePlanId][$date]['sales_type'] = $value ? 'ask_sell' : 'direct';
                $this->formData[$ratePlanId][$date]['ask_sell'] = $value;

                \Log::debug('Updated sales_type from ask_sell toggle', [
                    'ratePlanId' => $ratePlanId,
                    'date' => $date,
                    'ask_sell_value' => $value,
                    'sales_type_value' => $value ? 'ask_sell' : 'direct',
                ]);
            } elseif ($field === 'status') {
                // Handle the status toggle
                $this->formData[$ratePlanId][$date]['status'] = $value;

                \Log::debug('Updated status from toggle', [
                    'ratePlanId' => $ratePlanId,
                    'date' => $date,
                    'status_value' => $value,
                ]);
            } else {
                $this->formData[$ratePlanId][$date][$field] = $value;
            }
        }
    }

    /**
 * Livewire hooks to ensure the component responds properly to model changes
 */
public function updated($name, $value)
{
    // Toggle field updated

    // When ask_sell toggle changes, update the corresponding sales_type
    if (str_contains($name, '.ask_sell')) {
        // Extract ratePlanId and date from the name (format: formData.ratePlanId.date.ask_sell)
        $parts = explode('.', $name);
        if (count($parts) === 4) {
            $ratePlanId = $parts[1];
            $date = $parts[2];

            // Update the sales_type based on ask_sell value
            $this->formData[$ratePlanId][$date]['sales_type'] = $value ? 'ask_sell' : 'direct';

            // Updated sales_type based on ask_sell change
        }
    }
}

public function savePricing(): void
{
    try {
        // Basitleştirilmiş yaklaşım: Artık karmaşık period ya da istisna mantığını kullanmıyoruz
        // Doğrudan her gün için fiyat kaydediyoruz

        // Get the dailyRateService
        $dailyRateService = app(DailyRateService::class);

        $result = [
            'success' => true,
            'message' => 'Fiyatlar başarıyla kaydedildi.',
            'updatedRates' => 0,
            'errors' => [],
        ];

        // Processing form data

            foreach ($this->ratePlans as $ratePlanId => $ratePlan) {
                if (!isset($this->formData[$ratePlanId])) {
                    Log::warning('Rate plan not found in form data: ' . $ratePlanId);
                    continue;
                }

                // Process form data for rate plan

                $ratesData = [];
                foreach ($this->dateRange as $date) {
                    if (!isset($this->formData[$ratePlanId][$date])) {
                        continue;
                    }
                    
                    $dateData = $this->formData[$ratePlanId][$date];

                    // Processing date data

                    // Per-person pricing için prices dizisini kontrol et (eski mantık)
                    $basePrice = $dateData['base_price'] ?? 0;

                    // Is_per_person true ise ve prices dizisi varsa, ilk kişi değerini baz alıyoruz
                    if (($ratePlan['is_per_person'] ?? false) &&
                        isset($dateData['prices']) &&
                        is_array($dateData['prices']) &&
                        !empty($dateData['prices']) &&
                        isset($dateData['prices']['1'])) {

                        $basePrice = $dateData['prices']['1'];
                        // Used price from prices array due to is_per_person flag
                    }

                    // Process base price

                    if ($basePrice === null) {
                        $basePrice = 0;
                    }

                    // Additional handling for numeric/string conversion
                    if (is_string($basePrice) && trim($basePrice) === '') {
                        $basePrice = 0;
                    }

                    // Make sure it's numeric
                    $basePrice = is_numeric($basePrice) ? floatval($basePrice) : 0;

                    // Base price processed

                    // Check if refund selection is available
                    $isRefundable = true; // Default to refundable

                    // If we have a selected refund type in the rate plan
                    if (isset($this->ratePlans[$ratePlanId]['selected_refund_type'])) {
                        $isRefundable = $this->ratePlans[$ratePlanId]['selected_refund_type'] === 'refundable';

                        // Using selected refund type from rate plan
                    }
                    // If there's refund information in the date data
                    elseif (isset($dateData['refund_options']) && isset($dateData['refund_options']['is_refundable'])) {
                        $isRefundable = $dateData['refund_options']['is_refundable'];

                        // Using refund options from date data
                    }

                    // Check if we need to adjust price for non-refundable option
                    $nonRefundableDiscount = 0;
                    if (!$isRefundable && isset($this->hotelData['refund_policy']['non_refundable_discount'])) {
                        $nonRefundableDiscount = $this->hotelData['refund_policy']['non_refundable_discount'];

                        if ($nonRefundableDiscount > 0) {
                            // Apply discount if it's non-refundable
                            $discountMultiplier = (100 - $nonRefundableDiscount) / 100;
                            $originalPrice = $basePrice;
                            $basePrice = round($basePrice * $discountMultiplier, 2);

                            // Applied non-refundable discount
                        }
                    }

                    // Get price information
                    $prices = null;
                    if (($ratePlan['is_per_person'] ?? false) && isset($dateData['prices']) && is_array($dateData['prices'])) {
                        $prices = json_encode($dateData['prices']);
                        // Created prices_json for per-person pricing
                    } else {
                        // No prices array available or not per-person pricing
                    }

                    // Get sales type from ask_sell toggle or sales_type field
                    $salesType = 'direct'; // Default value

                    if (isset($dateData['ask_sell']) && $dateData['ask_sell'] === true) {
                        $salesType = 'ask_sell';
                    } elseif (isset($dateData['sales_type']) && $dateData['sales_type'] === 'ask_sell') {
                        $salesType = 'ask_sell';
                    }

                    // Processing sales type for date

                    $ratesData[$date] = [
                        'base_price' => $basePrice,
                        'currency' => 'TRY',
                        'is_closed' => !($dateData['status'] ?? true),
                        'min_stay_arrival' => $dateData['min_stay'] ?? 1,
                        'status' => $dateData['status'] ? 'available' : 'sold_out',
                        'sales_type' => $salesType, // Add sales_type based on ask_sell
                        'notes' => null,
                        'is_per_person' => $ratePlan['is_per_person'] ?? false,
                        'prices_json' => $prices,
                        'is_refundable' => $isRefundable,
                    ];

                    // Final rates data for date prepared
                }
                
                // Rate plan gerçekten var mı kontrol et
                try {
                    $ratePlan = \App\Plugins\Pricing\Models\RatePlan::findOrFail($ratePlanId);
                    // Rate plan exists and will be used

                    $saveResult = $dailyRateService->saveDailyRatesFromArray($ratePlanId, $ratesData);
                    if ($saveResult) {
                        $result['updatedRates'] += count($ratesData);
                    } else {
                        $result['success'] = false;
                        $result['errors'][] = "Fiyat verileri kaydedilemedi: RatePlan #" . $ratePlanId;
                    }
                } catch (\Exception $e) {
                    // Rate plan bulunamadı
                    \Log::error('Rate plan does not exist', [
                        'rate_plan_id' => $ratePlanId,
                        'error' => $e->getMessage()
                    ]);

                    $result['success'] = false;
                    $result['errors'][] = "RatePlan #" . $ratePlanId . " bulunamadı: " . $e->getMessage();
                }
            }

            if ($result['success']) {
                // Success notification
                Notification::make()
                    ->title('Fiyatlar başarıyla kaydedildi.')
                    ->body(sprintf(
                        '%d gün için fiyat bilgileri güncellendi.',
                        $result['updatedRates']
                    ))
                    ->success()
                    ->send();
            } else {
                // Error notification
                Notification::make()
                    ->title($result['message'])
                    ->body(
                        count($result['errors']) > 0
                            ? implode("\n", array_slice($result['errors'], 0, 3))
                            : 'Detaylar için loglara bakın.'
                    )
                    ->warning()
                    ->send();
            }

            // Signal parent component to refresh pricing data
            $this->dispatch('refreshPricingData');

        } catch (Exception $e) {
            // Log the full error for debugging
            \Log::error('Price saving error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Show a user-friendly notification
            $friendlyMessage = 'Fiyatlar kaydedilirken bir hata oluştu.';

            Notification::make()
                ->title($friendlyMessage)
                ->body($e->getMessage())
                ->warning()
                ->send();

            // Refresh data after error for a clean state
            $this->dispatch('refreshPricingData');
        }
    }

    /**
     * Update the refund type selection for a rate plan
     *
     * @param int $ratePlanId
     * @param string $refundType
     * @return void
     */
    public function updateRefundType(int $ratePlanId, string $refundType): void
    {
        try {
            // Validate the refund type
            if (!in_array($refundType, ['refundable', 'non_refundable'])) {
                Log::warning('Invalid refund type specified: ' . $refundType);
                return;
            }

            // Update the rate plan's refund type
            if (isset($this->ratePlans[$ratePlanId])) {
                $this->ratePlans[$ratePlanId]['selected_refund_type'] = $refundType;

                // Updated refund type for rate plan

                // If this is non-refundable, we might need to adjust prices displayed
                // based on the non-refundable discount
                if ($refundType === 'non_refundable') {
                    $nonRefundableDiscount = $this->ratePlans[$ratePlanId]['non_refundable_discount'] ??
                        ($this->hotelData['refund_policy']['non_refundable_discount'] ?? 0);

                    if ($nonRefundableDiscount > 0) {
                        // Non-refundable discount will be applied
                    }
                }

                // Force refresh to update UI
                $this->dispatch('$refresh');
            } else {
                Log::warning('Rate plan not found for refund type update: ' . $ratePlanId);
            }
        } catch (\Exception $e) {
            Log::error('Error updating refund type: ' . $e->getMessage());
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Tablo başlıklarında kullanılacak oda ve pansiyon tipi adlarını hazırla
        $tableHeaders = [];

        foreach ($this->ratePlans as $id => $plan) {
            $roomName = $plan['room_name'] ?? null;
            $boardTypeName = $plan['board_type_name'] ?? null;

            // Eğer room_name ve board_type_name henüz ekli değilse, doğrudan modellerden yükle
            if (!$roomName && isset($plan['room_id'])) {
                $room = \App\Plugins\Accommodation\Models\Room::find($plan['room_id']);
                $roomName = $room ? $room->name : 'Oda';
            }

            if (!$boardTypeName && isset($plan['board_type_id'])) {
                $boardType = \App\Plugins\Booking\Models\BoardType::find($plan['board_type_id']);
                $boardTypeName = $boardType ? $boardType->name : 'Pansiyon Tipi';
            }

            // Get pricing method label
            $pricingMethodLabel = '';
            if (isset($plan['is_per_person']) && $plan['is_per_person']) {
                $pricingMethodLabel = '(Kişi Başı Fiyatlandırma)';
            } else {
                $pricingMethodLabel = '(Oda Başı Fiyatlandırma)';
            }

            // Get refund type label
            $refundTypeLabel = '';
            if (isset($plan['selected_refund_type'])) {
                if ($plan['selected_refund_type'] === 'refundable') {
                    $refundTypeLabel = '(İade Edilebilir)';
                } else {
                    $refundTypeLabel = '(İade Edilemez)';
                }
            }

            // If both refundable and non-refundable are allowed, show refund selection in UI
            $showRefundSelection = false;
            if (isset($plan['allow_refundable']) && isset($plan['allow_non_refundable'])) {
                $showRefundSelection = $plan['allow_refundable'] && $plan['allow_non_refundable'];
            }

            $tableHeaders[$id] = [
                'room_name' => $roomName ?: 'Oda',
                'board_type_name' => $boardTypeName ?: 'Pansiyon Tipi',
                'pricing_method_label' => $pricingMethodLabel,
                'refund_type_label' => $refundTypeLabel,
                'show_refund_selection' => $showRefundSelection,
                'allow_refundable' => $plan['allow_refundable'] ?? true,
                'allow_non_refundable' => $plan['allow_non_refundable'] ?? false,
                'selected_refund_type' => $plan['selected_refund_type'] ?? 'refundable',
                'non_refundable_discount' => $plan['non_refundable_discount'] ?? ($this->hotelData['refund_policy']['non_refundable_discount'] ?? 0)
            ];
        }

        return view('filament.livewire.pricing-form', [
            'tableHeaders' => $tableHeaders
        ]);
    }
}