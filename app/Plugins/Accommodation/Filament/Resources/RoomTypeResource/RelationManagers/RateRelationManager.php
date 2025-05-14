<?php

namespace App\Plugins\Accommodation\Filament\Resources\RoomTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Pricing\Models\RatePlan;
use Illuminate\Support\Collection;

class RateRelationManager extends RelationManager
{
    protected static string $relationship = 'rates';

    protected static ?string $recordTitleAttribute = 'rate_name';

    protected static ?string $title = 'Tarifeler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('hotel_id')
                    ->label('Otel')
                    ->options(Hotel::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('season_id')
                    ->label('Sezon')
                    ->relationship('season', 'name')
                    ->searchable(),
                Forms\Components\Select::make('board_type_id')
                    ->label('Pansiyon Tipi')
                    ->relationship('boardType', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('rate_name')
                    ->label('Tarife Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rate_code')
                    ->label('Tarife Kodu')
                    ->maxLength(50),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Başlangıç Tarihi')
                            ->native(false)
                            ->required(),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Bitiş Tarihi')
                            ->native(false)
                            ->required()
                            ->after('date_from'),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('base_price')
                            ->label('Temel Fiyat')
                            ->numeric()
                            ->required()
                            ->step(0.01),
                        Forms\Components\Select::make('currency')
                            ->label('Para Birimi')
                            ->options([
                                'TRY' => 'TL',
                                'USD' => 'USD',
                                'EUR' => 'EUR', 
                                'GBP' => 'GBP',
                            ])
                            ->default('TRY')
                            ->required(),
                    ]),
                Forms\Components\Select::make('price_per')
                    ->label('Fiyatlandırma Birimi')
                    ->options([
                        'night' => 'Gece Başına',
                        'person' => 'Kişi Başına',
                        'unit' => 'Oda Başına',
                    ])
                    ->default('night')
                    ->required(),
                Forms\Components\Select::make('pricing_model')
                    ->label('Fiyatlandırma Modeli')
                    ->options([
                        'occupancy' => 'Doluluk Bazlı (Kişi sayısına göre değişir)',
                        'unit' => 'Birim Bazlı (Kişi sayısından bağımsız)',
                    ])
                    ->default('occupancy')
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('rate_type')
                    ->label('Fiyat Tipi')
                    ->options([
                        'refundable' => 'İade Edilebilir',
                        'non_refundable' => 'İade Edilemez',
                        'both' => 'Her İkisi',
                    ])
                    ->default('both')
                    ->required()
                    ->helperText('İade edilebilir: Normal fiyat. İade edilemez: İndirimli ama iptal edilemez. Her ikisi: Müşteri seçebilir.'),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('extra_adult_price')
                            ->label('Ekstra Yetişkin')
                            ->numeric()
                            ->default(0)
                            ->step(0.01),
                        Forms\Components\TextInput::make('extra_child_price')
                            ->label('Ekstra Çocuk')
                            ->numeric()
                            ->default(0)
                            ->step(0.01),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('min_nights')
                            ->label('Minimum Gece')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\TextInput::make('max_nights')
                            ->label('Maksimum Gece')
                            ->numeric(),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('min_adults')
                            ->label('Minimum Yetişkin')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\TextInput::make('max_adults')
                            ->label('Maksimum Yetişkin')
                            ->numeric(),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('min_children')
                            ->label('Minimum Çocuk')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('max_children')
                            ->label('Maksimum Çocuk')
                            ->numeric(),
                    ]),
                Forms\Components\CheckboxList::make('days_of_week')
                    ->label('Haftanın Günleri')
                    ->options([
                        1 => 'Pazartesi',
                        2 => 'Salı',
                        3 => 'Çarşamba',
                        4 => 'Perşembe',
                        5 => 'Cuma',
                        6 => 'Cumartesi',
                        7 => 'Pazar',
                    ])
                    ->columns(4),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        // is_refundable artık rate_type ile değiştirildi
                        Forms\Components\Toggle::make('is_refundable')
                            ->label('İade Edilebilir (Eski)')
                            ->default(true)
                            ->visible(false),
                    ]),
                Forms\Components\TextInput::make('prepayment_percentage')
                    ->label('Ön Ödeme Yüzdesi (%)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Textarea::make('cancellation_policy')
                    ->label('İptal Politikası')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Otel')
                    ->sortable(),
                Tables\Columns\TextColumn::make('season.name')
                    ->label('Sezon')
                    ->sortable(),
                Tables\Columns\TextColumn::make('boardType.name')
                    ->label('Pansiyon')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_name')
                    ->label('Tarife Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_from')
                    ->label('Başlangıç')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_to')
                    ->label('Bitiş')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Fiyat')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_per')
                    ->label('Fiyat Birimi')
                    ->formatStateUsing(fn ($state): string => match ($state ?? '') {
                        'night' => 'Gece',
                        'person' => 'Kişi',
                        'unit' => 'Oda',
                        '' => 'Belirtilmemiş',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('pricing_model')
                    ->label('Fiyat Modeli')
                    ->formatStateUsing(fn ($state): string => match ($state ?? '') {
                        'occupancy' => 'Doluluk',
                        'unit' => 'Birim',
                        '' => 'Belirtilmemiş',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('rate_type')
                    ->label('İade')
                    ->formatStateUsing(fn ($state): string => match ($state ?? '') {
                        'refundable' => 'İade Edilebilir',
                        'non_refundable' => 'İade Edilemez',
                        'both' => 'Her İkisi',
                        '' => 'Belirtilmemiş',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hotel_id')
                    ->label('Otel')
                    ->relationship('hotel', 'name'),
                Tables\Filters\SelectFilter::make('season_id')
                    ->label('Sezon')
                    ->relationship('season', 'name'),
                Tables\Filters\SelectFilter::make('board_type_id')
                    ->label('Pansiyon Tipi')
                    ->relationship('boardType', 'name'),
                Tables\Filters\SelectFilter::make('rate_type')
                    ->label('İade Tipi')
                    ->options([
                        'refundable' => 'İade Edilebilir',
                        'non_refundable' => 'İade Edilemez',
                        'both' => 'Her İkisi',
                    ]),
                Tables\Filters\SelectFilter::make('pricing_model')
                    ->label('Fiyat Modeli')
                    ->options([
                        'occupancy' => 'Doluluk Bazlı',
                        'unit' => 'Birim Bazlı',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
                Tables\Filters\Filter::make('date_range')
                    ->label('Tarih Aralığı')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Başlangıç')
                            ->native(false),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Bitiş')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->where('date_from', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->where('date_to', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Kopyala')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function ($record) {
                        $newRecord = $record->replicate();
                        $newRecord->rate_name = $newRecord->rate_name . ' (Kopya)';
                        $newRecord->save();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktif Et')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Et')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),
                ]),
            ]);
    }
}