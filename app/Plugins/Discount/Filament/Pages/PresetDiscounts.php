<?php

namespace App\Plugins\Discount\Filament\Pages;

use App\Plugins\Discount\Enums\DiscountType;
use App\Plugins\Discount\Enums\StackType;
use App\Plugins\Discount\Models\Discount;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Booking\Models\BoardType;
use App\Plugins\Discount\Services\DiscountService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;

class PresetDiscounts extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static string $view = 'filament.pages.preset-discounts';

    protected static ?string $navigationGroup = 'Pricing';

    protected static ?int $navigationSort = 31;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => '',
            'description' => '',
            'value' => 0,
            'discount_type' => '',
            'stack_type' => 'STACKABLE',
            'configuration' => [],
        ]);
    }

    public function form(Form $form): Form
    {
        $presets = Config::get('discount.presets', []);
        $presetOptions = [];

        foreach ($presets as $key => $preset) {
            $presetOptions[$key] = $preset['name'] ?? Str::title(str_replace('_', ' ', $key));
        }

        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Select a Preset')
                            ->description('Choose from our predefined discount templates')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\Select::make('preset')
                                    ->label('Preset Type')
                                    ->options($presetOptions)
                                    ->required()
                                    ->default(array_key_first($presetOptions))
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $preset = Config::get("discount.presets.{$state}");
                                        if (!$preset) {
                                            return;
                                        }

                                        // Set default values from preset
                                        $set('name', $preset['name'] ?? '');
                                        $set('description', $preset['description'] ?? '');

                                        if (isset($preset['discount_type'])) {
                                            $set('discount_type', $preset['discount_type']);
                                        }

                                        if (isset($preset['value'])) {
                                            $set('value', $preset['value']);
                                        }

                                        if (isset($preset['stack_type'])) {
                                            $set('stack_type', $preset['stack_type']);
                                        }

                                        if (isset($preset['configuration'])) {
                                            $set('configuration', $preset['configuration']);
                                        }
                                    }),

                                Forms\Components\Hidden::make('discount_type')
                                    ->required(),
                                Forms\Components\Hidden::make('stack_type')
                                    ->required(),
                                Forms\Components\Hidden::make('configuration'),
                            ]),

                        Forms\Components\Section::make('Customize Discount')
                            ->description('Adjust the discount details')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Discount Name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->maxLength(1000),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('value')
                                            ->label('Value')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('start_date')
                                                    ->label('Start Date'),

                                                Forms\Components\DateTimePicker::make('end_date')
                                                    ->label('End Date')
                                                    ->after('start_date'),
                                            ]),
                                    ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Apply Discount To')
                    ->description('Select where this discount will be applied')
                    ->schema([
                        Forms\Components\Repeater::make('targets')
                            ->label('Apply To')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('target_type')
                                            ->label('Target Type')
                                            ->options([
                                                'ALL' => 'All Items',
                                                'HOTEL' => 'Specific Hotel',
                                                'ROOM_TYPE' => 'Specific Room Type',
                                                'BOARD_TYPE' => 'Specific Board Type',
                                            ])
                                            ->required()
                                            ->live(),

                                        Forms\Components\Select::make('hotel_filter')
                                            ->label('Filter by Hotel')
                                            ->options(\App\Plugins\Accommodation\Models\Hotel::pluck('name', 'id'))
                                            ->searchable()
                                            ->visible(fn (Forms\Get $get) => in_array($get('target_type'), ['ROOM_TYPE']))
                                            ->live(),

                                        Forms\Components\Select::make('target_id')
                                            ->label('Target')
                                            ->options(function (Forms\Get $get) {
                                                $targetType = $get('target_type');
                                                $hotelFilter = $get('hotel_filter');

                                                if (!$targetType || $targetType === 'ALL') {
                                                    return [];
                                                }

                                                return match ($targetType) {
                                                    'HOTEL' => \App\Plugins\Accommodation\Models\Hotel::pluck('name', 'id'),
                                                    'ROOM_TYPE' => $hotelFilter
                                                        ? \App\Plugins\Accommodation\Models\RoomType::whereHas('rooms', function ($query) use ($hotelFilter) {
                                                            $query->where('hotel_id', $hotelFilter);
                                                        })->pluck('name', 'id')
                                                        : \App\Plugins\Accommodation\Models\RoomType::pluck('name', 'id'),
                                                    'BOARD_TYPE' => \App\Plugins\Booking\Models\BoardType::pluck('name', 'id'),
                                                    default => [],
                                                };
                                            })
                                            ->searchable()
                                            ->required(fn (Forms\Get $get) => $get('target_type') !== 'ALL')
                                            ->visible(fn (Forms\Get $get) => $get('target_type') !== 'ALL'),
                                    ]),
                            ])
                            ->itemLabel(fn (array $state): ?string => match ($state['target_type'] ?? null) {
                                'ALL' => 'All Items',
                                'HOTEL' => 'Hotel: ' . (
                                    $state['target_type'] === 'HOTEL' && $state['target_id']
                                    ? \App\Plugins\Accommodation\Models\Hotel::find($state['target_id'])?->name ?? 'Unknown'
                                    : ''
                                ),
                                'ROOM_TYPE' => 'Room Type: ' . (
                                    $state['target_type'] === 'ROOM_TYPE' && $state['target_id']
                                    ? \App\Plugins\Accommodation\Models\RoomType::find($state['target_id'])?->name ?? 'Unknown'
                                    : ''
                                ),
                                'BOARD_TYPE' => 'Board Type: ' . (
                                    $state['target_type'] === 'BOARD_TYPE' && $state['target_id']
                                    ? \App\Plugins\Booking\Models\BoardType::find($state['target_id'])?->name ?? 'Unknown'
                                    : ''
                                ),
                                default => null,
                            })
                            ->defaultItems(1)
                            ->columns(1),
                    ]),
            ]);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        try {
            // Get the preset configuration if no discount type is explicitly set
            if (empty($data['discount_type']) && !empty($data['preset'])) {
                $preset = Config::get("discount.presets.{$data['preset']}");
                if ($preset) {
                    $data['discount_type'] = $preset['discount_type'] ?? 'percentage';
                    $data['stack_type'] = $preset['stack_type'] ?? StackType::STACKABLE->value;
                    $data['configuration'] = $preset['configuration'] ?? [];
                }
            }

            // Create the discount
            $discount = Discount::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'discount_type' => $data['discount_type'],
                'value' => $data['value'],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'is_active' => true,
                'priority' => 0,
                'stack_type' => $data['stack_type'] ?? StackType::STACKABLE->value,
                'configuration' => $data['configuration'] ?? [],
            ]);
            
            // Create targets
            if (isset($data['targets']) && is_array($data['targets'])) {
                foreach ($data['targets'] as $target) {
                    $discount->targets()->create([
                        'target_type' => $target['target_type'],
                        'target_id' => $target['target_type'] === 'ALL' ? null : $target['target_id'],
                    ]);
                }
            }
            
            Notification::make()
                ->title('Discount created successfully')
                ->success()
                ->send();
                
            $this->form->fill([
                'name' => '',
                'description' => '',
                'value' => 0,
                'discount_type' => '',
                'stack_type' => 'STACKABLE',
                'configuration' => [],
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error creating discount')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}