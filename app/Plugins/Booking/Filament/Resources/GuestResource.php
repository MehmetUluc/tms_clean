<?php

namespace App\Plugins\Booking\Filament\Resources;

use App\Plugins\Booking\Filament\Resources\GuestResource\Pages;
use App\Plugins\Booking\Filament\Resources\GuestResource\RelationManagers;
use App\Plugins\Booking\Models\Guest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;
    
    protected static ?string $modelLabel = 'Misafir';
    protected static ?string $pluralModelLabel = 'Misafirler';
    protected static ?string $navigationLabel = 'Misafirler';

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Rezervasyon Yönetimi';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Misafir Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Ad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Soyad')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Doğum Tarihi')
                            ->native(false),
                        Forms\Components\TextInput::make('age')
                            ->label('Yaş')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\Select::make('gender')
                            ->label('Cinsiyet')
                            ->options([
                                'male' => 'Erkek',
                                'female' => 'Kadın',
                                'other' => 'Diğer',
                            ]),
                        Forms\Components\Select::make('guest_type')
                            ->label('Misafir Tipi')
                            ->options([
                                'adult' => 'Yetişkin',
                                'child' => 'Çocuk',
                                'infant' => 'Bebek',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_primary')
                            ->label('Ana Misafir')
                            ->helperText('Bu misafir, rezervasyonun ana sorumlusu mu?'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('İletişim Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Adres')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Şehir')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('country')
                            ->label('Ülke')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Kimlik Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('id_number')
                            ->label('Kimlik/Pasaport No')
                            ->maxLength(255),
                        Forms\Components\Select::make('id_type')
                            ->label('Kimlik Tipi')
                            ->options([
                                'national_id' => 'T.C. Kimlik',
                                'passport' => 'Pasaport',
                                'driver_license' => 'Ehliyet',
                                'other' => 'Diğer',
                            ]),
                        Forms\Components\TextInput::make('nationality')
                            ->label('Uyruk')
                            ->maxLength(255),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Rezervasyon Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('reservation_id')
                            ->label('Rezervasyon')
                            ->relationship('reservation', 'reservation_number')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextInputColumn::make('first_name')
                    ->label('Ad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('last_name')
                    ->label('Soyad')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reservation.reservation_number')
                    ->label('Rezervasyon No')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->reservation ? 
                        route('filament.admin.resources.reservations.edit', ['record' => $record->reservation->id]) : null),
                Tables\Columns\TextColumn::make('reservation.hotel.name')
                    ->label('Otel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('nationality')
                    ->label('Uyruk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Doğum Tarihi')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('Yaş')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('guest_type')
                    ->label('Misafir Tipi')
                    ->options([
                        'adult' => 'Yetişkin',
                        'child' => 'Çocuk',
                        'infant' => 'Bebek',
                    ])
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_primary')
                    ->label('Ana Misafir')
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->type('email'),
                Tables\Columns\TextInputColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->type('tel'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordClasses(fn (Guest $record) => $record->is_primary ? 'bg-primary-50' : null)
            ->defaultPaginationPageOption(25)
            // Satıra tıklandığında edit sayfasını açar
            ->recordAction('edit')
            // Tablonun hızlı düzenleme modunu etkinleştir (Filament 3'te böyle bir metod yok, o yüzden kaldırıyoruz)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guest_type')
                    ->label('Misafir Tipi')
                    ->options([
                        'adult' => 'Yetişkin',
                        'child' => 'Çocuk',
                        'infant' => 'Bebek',
                    ]),
                Tables\Filters\SelectFilter::make('is_primary')
                    ->label('Ana Misafir')
                    ->options([
                        '1' => 'Evet',
                        '0' => 'Hayır',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListGuests::route('/'),
            'view' => Pages\ViewGuest::route('/{record}'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // Misafir oluşturma özelliğini devre dışı bırak
    }
}