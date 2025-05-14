<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelResource\RelationManagers;

use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Accommodation\Filament\Resources\RoomResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';
    
    protected static ?string $title = 'Odalar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Oda Bilgileri')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'vertical-tabs'])
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Temel Bilgiler')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Oda Adı')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('room_number')
                                    ->label('Oda Numarası')
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('floor')
                                    ->label('Kat')
                                    ->maxLength(50),
                                Forms\Components\Select::make('room_type_id')
                                    ->label('Oda Tipi')
                                    ->relationship('roomType', 'name')
                                    ->required()
                                    ->preload()
                                    ->searchable(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Kapasite ve Fiyat')
                            ->schema([
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
                                    ->minValue(0)
                                    ->required(),
                                Forms\Components\TextInput::make('size')
                                    ->label('Oda Büyüklüğü (m²)')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('base_price')
                                    ->label('Taban Fiyat')
                                    ->numeric()
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
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Açıklama')
                            ->schema([
                                Forms\Components\RichEditor::make('description')
                                    ->label('Açıklama')
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Görseller')
                            ->schema([
                                Forms\Components\FileUpload::make('cover_image')
                                    ->label('Kapak Görseli')
                                    ->image()
                                    ->directory('rooms/covers'),
                                Forms\Components\FileUpload::make('gallery')
                                    ->label('Galeri')
                                    ->multiple()
                                    ->directory('rooms/gallery'),
                            ]),
                            
                        // Pansiyon Tipleri sekmesi kaldırıldı - PricingV2 ile yeni mimari kullanılacak
                            
                        Forms\Components\Tabs\Tab::make('Durum')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_available')
                                    ->label('Müsait')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('Öne Çıkan')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Oda Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('Oda No'),
                Tables\Columns\TextColumn::make('floor')
                    ->label('Kat'),
                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Oda Tipi')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity_adults')
                    ->label('Yetişkin')
                    ->icon('heroicon-o-user')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('capacity_children')
                    ->label('Çocuk')
                    ->icon('heroicon-o-user-minus')
                    ->alignCenter(),
                // Pansiyon Tipleri sütunu kaldırıldı - PricingV2 ile yeni mimari kullanılacak
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Taban Fiyat')
                    ->money(fn ($record) => $record->currency)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Müsait')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('room_type_id')
                    ->label('Oda Tipi')
                    ->relationship('roomType', 'name')
                    ->preload()
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif Odalar')
                    ->placeholder('Tüm Odalar')
                    ->trueLabel('Aktif Odalar')
                    ->falseLabel('Pasif Odalar'),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Müsait Odalar')
                    ->placeholder('Tüm Odalar')
                    ->trueLabel('Müsait Odalar')
                    ->falseLabel('Müsait Olmayan Odalar'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('xl'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Room $record): string => RoomResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('aktifleştir')
                        ->label('Aktifleştir')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('pasifleştir')
                        ->label('Pasifleştir')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}