<?php

namespace App\Plugins\Discount\Filament\Resources;

use App\Plugins\Discount\Filament\Resources\DiscountResource\Pages;
use App\Plugins\Discount\Models\Discount;
use App\Plugins\Discount\Enums\DiscountType;
use App\Plugins\Discount\Enums\StackType;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Booking\Models\BoardType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Pricing';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                Forms\Components\Select::make('discount_type')
                                    ->label('Discount Type')
                                    ->options(DiscountType::options())
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Forms\Set $set) => $set('configuration', []))
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(1000)
                            ->columnSpan('full'),
                    ]),

                Forms\Components\Tabs::make('discount_tabs')
                    ->tabs([
                        // Main tab with discount details
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Discount Value')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('value')
                                                    ->label(fn (Forms\Get $get) => match ($get('discount_type')) {
                                                        'percentage' => 'Percentage Value (%)',
                                                        'fixed_amount' => 'Fixed Amount',
                                                        'free_nights' => 'Number of Free Nights',
                                                        'nth_night_free' => 'Nth Night (e.g., 3 for 3rd night free)',
                                                        default => 'Value',
                                                    })
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(0)
                                                    ->maxValue(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? 100 : null),

                                                Forms\Components\TextInput::make('max_value')
                                                    ->label('Maximum Discount Amount')
                                                    ->helperText('Leave empty for no maximum')
                                                    ->numeric()
                                                    ->minValue(0),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Discount Period')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('start_date')
                                                    ->label('Start Date')
                                                    ->helperText('Leave empty to start immediately'),

                                                Forms\Components\DateTimePicker::make('end_date')
                                                    ->label('End Date')
                                                    ->helperText('Leave empty for no end date')
                                                    ->after('start_date'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Discount Settings')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true),

                                        Forms\Components\Select::make('stack_type')
                                            ->label('Stack Type')
                                            ->options(StackType::options())
                                            ->required()
                                            ->default(StackType::STACKABLE->value),

                                        Forms\Components\TextInput::make('priority')
                                            ->label('Priority')
                                            ->helperText('Higher priority discounts are applied first')
                                            ->numeric()
                                            ->integer()
                                            ->default(0),

                                        Forms\Components\TextInput::make('min_booking_value')
                                            ->label('Minimum Booking Value')
                                            ->helperText('Minimum order value required to apply discount')
                                            ->numeric()
                                            ->minValue(0),

                                        Forms\Components\TextInput::make('max_uses_total')
                                            ->label('Maximum Total Uses')
                                            ->helperText('Leave empty for unlimited uses')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0),

                                        Forms\Components\TextInput::make('max_uses_per_user')
                                            ->label('Maximum Uses Per User')
                                            ->helperText('Leave empty for unlimited uses per user')
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0),
                                    ]),
                            ]),

                        // Configuration tab - dynamic based on discount type
                        Forms\Components\Tabs\Tab::make('Configuration')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema(function (Forms\Get $get) {
                                $discountType = $get('discount_type');

                                if (!$discountType) {
                                    return [
                                        Forms\Components\Placeholder::make('configuration_message')
                                            ->content('Please select a discount type first.'),
                                    ];
                                }

                                return match ($discountType) {
                                    'percentage' => [
                                        Forms\Components\Section::make('Percentage Discount Configuration')
                                            ->description('No additional configuration required for percentage discounts.')
                                            ->schema([]),
                                    ],

                                    'fixed_amount' => [
                                        Forms\Components\Section::make('Fixed Amount Discount Configuration')
                                            ->description('No additional configuration required for fixed amount discounts.')
                                            ->schema([]),
                                    ],

                                    'free_nights' => [
                                        Forms\Components\Section::make('Free Nights Discount Configuration')
                                            ->description('This discount will apply the specified number of free nights to the total stay.')
                                            ->schema([]),
                                    ],

                                    'nth_night_free' => [
                                        Forms\Components\Section::make('Nth Night Free Configuration')
                                            ->schema([
                                                Forms\Components\TextInput::make('configuration.max_free_nights')
                                                    ->label('Maximum Free Nights')
                                                    ->helperText('Maximum number of free nights that can be applied. Leave empty for no limit.')
                                                    ->numeric()
                                                    ->integer()
                                                    ->minValue(0),
                                            ]),
                                    ],

                                    'early_booking' => [
                                        Forms\Components\Section::make('Early Booking Discount Configuration')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('configuration.days_threshold')
                                                            ->label('Days Threshold')
                                                            ->helperText('Minimum days before check-in for early booking discount')
                                                            ->required()
                                                            ->numeric()
                                                            ->integer()
                                                            ->minValue(1)
                                                            ->default(30),

                                                        Forms\Components\TextInput::make('configuration.additional_percent_per_day')
                                                            ->label('Additional % Per Extra Day')
                                                            ->helperText('Additional percentage for each day earlier than the threshold')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(10)
                                                            ->default(0),

                                                        Forms\Components\TextInput::make('configuration.max_additional_percent')
                                                            ->label('Maximum Additional %')
                                                            ->helperText('Maximum additional percentage that can be added')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(50)
                                                            ->default(0),
                                                    ]),
                                            ]),
                                    ],

                                    'last_minute' => [
                                        Forms\Components\Section::make('Last Minute Discount Configuration')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('configuration.max_days_before_check_in')
                                                            ->label('Maximum Days Before Check-In')
                                                            ->helperText('Maximum days before check-in for last minute discount')
                                                            ->required()
                                                            ->numeric()
                                                            ->integer()
                                                            ->minValue(1)
                                                            ->default(7),

                                                        Forms\Components\TextInput::make('configuration.min_days_before_check_in')
                                                            ->label('Minimum Days Before Check-In')
                                                            ->helperText('Minimum days before check-in for last minute discount')
                                                            ->numeric()
                                                            ->integer()
                                                            ->minValue(0)
                                                            ->default(0),

                                                        Forms\Components\TextInput::make('configuration.additional_percent_per_day_closer')
                                                            ->label('Additional % Per Day Closer')
                                                            ->helperText('Additional percentage for each day closer to check-in')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(10)
                                                            ->default(0),
                                                    ]),
                                            ]),
                                    ],

                                    'long_stay' => [
                                        Forms\Components\Section::make('Long Stay Discount Configuration')
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('configuration.min_nights')
                                                            ->label('Minimum Nights')
                                                            ->helperText('Minimum nights required for long stay discount')
                                                            ->required()
                                                            ->numeric()
                                                            ->integer()
                                                            ->minValue(1)
                                                            ->default(7),

                                                        Forms\Components\TextInput::make('configuration.additional_percent_per_extra_night')
                                                            ->label('Additional % Per Extra Night')
                                                            ->helperText('Additional percentage for each extra night beyond the minimum')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(5)
                                                            ->default(0),

                                                        Forms\Components\TextInput::make('configuration.max_additional_percent')
                                                            ->label('Maximum Additional %')
                                                            ->helperText('Maximum additional percentage that can be added')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(50)
                                                            ->default(0),
                                                    ]),
                                            ]),
                                    ],

                                    'package_deal' => [
                                        Forms\Components\Section::make('Package Deal Configuration')
                                            ->schema([
                                                Forms\Components\CheckboxList::make('configuration.required_services')
                                                    ->label('Required Services')
                                                    ->helperText('Services that must be booked to apply this discount')
                                                    ->options([
                                                        'airport_transfer' => 'Airport Transfer',
                                                        'breakfast' => 'Breakfast',
                                                        'dinner' => 'Dinner',
                                                        'spa' => 'Spa Services',
                                                        'excursion' => 'Excursion',
                                                    ])
                                                    ->required(),
                                            ]),
                                    ],

                                    default => [
                                        Forms\Components\Section::make('Configuration')
                                            ->description('No additional configuration available for this discount type.')
                                            ->schema([]),
                                    ],
                                };
                            }),

                        // Targets tab
                        Forms\Components\Tabs\Tab::make('Targets')
                            ->icon('heroicon-o-cursor-arrow-rays')
                            ->schema([
                                Forms\Components\Section::make('Apply Discount To')
                                    ->description('Select where this discount will be applied')
                                    ->schema([
                                        Forms\Components\Repeater::make('targets')
                                            ->relationship()
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
                                                            ->options(Hotel::pluck('name', 'id'))
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
                                                                    'HOTEL' => Hotel::pluck('name', 'id'),
                                                                    'ROOM_TYPE' => $hotelFilter
                                                                        ? RoomType::whereHas('rooms', function ($query) use ($hotelFilter) {
                                                                            $query->where('hotel_id', $hotelFilter);
                                                                        })->pluck('name', 'id')
                                                                        : RoomType::pluck('name', 'id'),
                                                                    'BOARD_TYPE' => BoardType::pluck('name', 'id'),
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
                                                'HOTEL' => 'Hotel: ' . Hotel::find($state['target_id'] ?? 0)?->name ?? 'Unknown',
                                                'ROOM_TYPE' => 'Room Type: ' . RoomType::find($state['target_id'] ?? 0)?->name ?? 'Unknown',
                                                'BOARD_TYPE' => 'Board Type: ' . BoardType::find($state['target_id'] ?? 0)?->name ?? 'Unknown',
                                                default => null,
                                            })
                                            ->defaultItems(1)
                                            ->collapsed(),
                                    ]),
                            ]),

                        // Conditions tab
                        Forms\Components\Tabs\Tab::make('Conditions')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Forms\Components\Section::make('Discount Conditions')
                                    ->description('Add conditions that must be met for this discount to apply')
                                    ->schema([
                                        Forms\Components\Repeater::make('conditions')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\Select::make('condition_type')
                                                            ->label('Condition Type')
                                                            ->options([
                                                                'MIN_STAY' => 'Minimum Stay',
                                                                'MAX_STAY' => 'Maximum Stay',
                                                                'SPECIFIC_DAYS' => 'Specific Days of Week',
                                                                'CHECK_IN_DAYS' => 'Check-In Days',
                                                                'CHECK_OUT_DAYS' => 'Check-Out Days',
                                                                'MIN_GUESTS' => 'Minimum Guests',
                                                                'MAX_GUESTS' => 'Maximum Guests',
                                                                'MIN_ROOMS' => 'Minimum Rooms',
                                                                'MAX_ROOMS' => 'Maximum Rooms',
                                                                'MIN_ADVANCE_DAYS' => 'Minimum Days in Advance',
                                                                'MAX_ADVANCE_DAYS' => 'Maximum Days in Advance',
                                                            ])
                                                            ->required()
                                                            ->live(),

                                                        Forms\Components\Select::make('operator')
                                                            ->label('Operator')
                                                            ->options(function (Forms\Get $get) {
                                                                $conditionType = $get('condition_type');

                                                                if (!$conditionType) {
                                                                    return [];
                                                                }

                                                                return match ($conditionType) {
                                                                    'SPECIFIC_DAYS', 'CHECK_IN_DAYS', 'CHECK_OUT_DAYS' => [
                                                                        'IN' => 'Is One Of',
                                                                        'NOT_IN' => 'Is Not One Of',
                                                                    ],
                                                                    default => [
                                                                        'EQUALS' => 'Equals',
                                                                        'NOT_EQUALS' => 'Not Equals',
                                                                        'GREATER_THAN' => 'Greater Than',
                                                                        'LESS_THAN' => 'Less Than',
                                                                        'GREATER_THAN_OR_EQUALS' => 'Greater Than or Equals',
                                                                        'LESS_THAN_OR_EQUALS' => 'Less Than or Equals',
                                                                    ],
                                                                };
                                                            })
                                                            ->required(),
                                                    ]),

                                                Forms\Components\Hidden::make('value')
                                                    ->default([]),

                                                // Dynamic value field based on condition type
                                                Forms\Components\TextInput::make('numeric_value')
                                                    ->label('Value')
                                                    ->numeric()
                                                    ->integer()
                                                    ->required()
                                                    ->visible(fn (Forms\Get $get) => in_array($get('condition_type'), [
                                                        'MIN_STAY', 'MAX_STAY', 'MIN_GUESTS', 'MAX_GUESTS',
                                                        'MIN_ROOMS', 'MAX_ROOMS', 'MIN_ADVANCE_DAYS', 'MAX_ADVANCE_DAYS',
                                                    ]))
                                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $state) {
                                                        $set('value', [$state]);
                                                    }),

                                                Forms\Components\CheckboxList::make('days_value')
                                                    ->label('Days')
                                                    ->options([
                                                        0 => 'Sunday',
                                                        1 => 'Monday',
                                                        2 => 'Tuesday',
                                                        3 => 'Wednesday',
                                                        4 => 'Thursday',
                                                        5 => 'Friday',
                                                        6 => 'Saturday',
                                                    ])
                                                    ->required()
                                                    ->visible(fn (Forms\Get $get) => in_array($get('condition_type'), [
                                                        'SPECIFIC_DAYS', 'CHECK_IN_DAYS', 'CHECK_OUT_DAYS',
                                                    ]))
                                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $state) {
                                                        $set('value', $state);
                                                    }),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => match ($state['condition_type'] ?? null) {
                                                'MIN_STAY' => 'Minimum Stay: ' . json_encode($state['value'] ?? []),
                                                'MAX_STAY' => 'Maximum Stay: ' . json_encode($state['value'] ?? []),
                                                'SPECIFIC_DAYS' => 'Specific Days: ' . implode(', ', array_map(fn ($day) => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] ?? '', $state['value'] ?? [])),
                                                'CHECK_IN_DAYS' => 'Check-In Days: ' . implode(', ', array_map(fn ($day) => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] ?? '', $state['value'] ?? [])),
                                                'CHECK_OUT_DAYS' => 'Check-Out Days: ' . implode(', ', array_map(fn ($day) => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day] ?? '', $state['value'] ?? [])),
                                                'MIN_GUESTS' => 'Minimum Guests: ' . json_encode($state['value'] ?? []),
                                                'MAX_GUESTS' => 'Maximum Guests: ' . json_encode($state['value'] ?? []),
                                                'MIN_ROOMS' => 'Minimum Rooms: ' . json_encode($state['value'] ?? []),
                                                'MAX_ROOMS' => 'Maximum Rooms: ' . json_encode($state['value'] ?? []),
                                                'MIN_ADVANCE_DAYS' => 'Minimum Advance Days: ' . json_encode($state['value'] ?? []),
                                                'MAX_ADVANCE_DAYS' => 'Maximum Advance Days: ' . json_encode($state['value'] ?? []),
                                                default => null,
                                            })
                                            ->collapsed(),
                                    ]),
                            ]),

                        // Discount Codes tab
                        Forms\Components\Tabs\Tab::make('Discount Codes')
                            ->icon('heroicon-o-ticket')
                            ->schema([
                                Forms\Components\Section::make('Discount Codes')
                                    ->description('Create promo codes for this discount')
                                    ->schema([
                                        Forms\Components\Repeater::make('codes')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('code')
                                                            ->label('Code')
                                                            ->required()
                                                            ->maxLength(50)
                                                            ->alphaDash()
                                                            ->formatStateUsing(fn ($state) => Str::upper($state))
                                                            ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('code', Str::upper($state))),

                                                        Forms\Components\TextInput::make('max_uses')
                                                            ->label('Maximum Uses')
                                                            ->helperText('0 for unlimited uses')
                                                            ->numeric()
                                                            ->integer()
                                                            ->minValue(0)
                                                            ->default(0),

                                                        Forms\Components\Toggle::make('is_active')
                                                            ->label('Active')
                                                            ->default(true),
                                                    ]),

                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\DateTimePicker::make('start_date')
                                                            ->label('Start Date')
                                                            ->helperText('Leave empty to start immediately'),

                                                        Forms\Components\DateTimePicker::make('end_date')
                                                            ->label('End Date')
                                                            ->helperText('Leave empty for no end date')
                                                            ->after('start_date'),
                                                    ]),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['code'] ?? null)
                                            ->defaultItems(0)
                                            ->collapsed(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),
                
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->formatStateUsing(function ($state, Discount $record) {
                        return match ($record->discount_type) {
                            DiscountType::PERCENTAGE => "{$state}%",
                            DiscountType::FIXED_AMOUNT => "₺{$state}",
                            DiscountType::FREE_NIGHTS => "{$state} " . Str::plural('night', $state),
                            DiscountType::NTH_NIGHT_FREE => "Every {$state}th night free",
                            default => $state,
                        };
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('stack_type')
                    ->label('Stack')
                    ->formatStateUsing(fn ($state) => ucfirst(strtolower($state))),
                
                Tables\Columns\TextColumn::make('codes_count')
                    ->label('Codes')
                    ->counts('codes'),
                
                Tables\Columns\TextColumn::make('usages_count')
                    ->label('Uses')
                    ->counts('usages'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_type')
                    ->multiple()
                    ->options(DiscountType::options()),
                
                Tables\Filters\SelectFilter::make('stack_type')
                    ->multiple()
                    ->options(StackType::options()),
                
                Tables\Filters\Filter::make('is_active')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Tables\Filters\Filter::make('current')
                    ->label('Currently Active')
                    ->query(function (Builder $query): Builder {
                        return $query->where('is_active', true)
                            ->where(function ($query) {
                                $query->whereNull('start_date')
                                    ->orWhere('start_date', '<=', now());
                            })
                            ->where(function ($query) {
                                $query->whereNull('end_date')
                                    ->orWhere('end_date', '>=', now());
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'view' => Pages\ViewDiscount::route('/{record}'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }
}