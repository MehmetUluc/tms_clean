<?php

namespace App\Plugins\Amenities\Filament\Resources\RoomAmenityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Plugins\Accommodation\Models\Room;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = 'Odalar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('hotel_id')
                    ->label('Otel')
                    ->relationship('hotel', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('room_type_id')
                    ->label('Oda Tipi')
                    ->relationship('roomType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Oda Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('room_number')
                    ->label('Oda Numarası')
                    ->maxLength(50),
                Forms\Components\TextInput::make('floor')
                    ->label('Kat')
                    ->numeric()
                    ->minValue(0),
                Forms\Components\TextInput::make('capacity_adults')
                    ->label('Yetişkin Kapasitesi')
                    ->numeric()
                    ->default(2)
                    ->minValue(1)
                    ->required(),
                Forms\Components\TextInput::make('capacity_children')
                    ->label('Çocuk Kapasitesi')
                    ->numeric()
                    ->default(1)
                    ->minValue(0),
                Forms\Components\TextInput::make('base_price')
                    ->label('Temel Fiyat')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->default(0),
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
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Forms\Components\Toggle::make('is_available')
                    ->label('Müsait')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Otel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Oda Tipi')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Oda Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Oda No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity_adults')
                    ->label('Yetişkin')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('👤', $state) : '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity_children')
                    ->label('Çocuk')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('👶', $state) : '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Temel Fiyat')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                Tables\Columns\ToggleColumn::make('is_available')
                    ->label('Müsait'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hotel_id')
                    ->label('Otel')
                    ->relationship('hotel', 'name'),
                Tables\Filters\SelectFilter::make('room_type_id')
                    ->label('Oda Tipi')
                    ->relationship('roomType', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Müsait'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Oda Ekle')
                    ->recordSelect(function (Tables\Actions\AttachAction $action) {
                        return $action->recordSelect()
                            ->placeholder('Oda seçin...')
                            ->options(function () {
                                return Room::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Bağlantıyı Kaldır'),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Bağlantıları Kaldır'),
                ]),
            ]);
    }
}