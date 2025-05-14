<?php

namespace App\Plugins\Booking\Filament\Resources\ReservationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GuestsRelationManager extends RelationManager
{
    protected static string $relationship = 'guests';

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $title = 'Misafirler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('is_primary')
                    ->label('Ana Misafir')
                    ->default(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, RelationManager $livewire) {
                        if ($state) {
                            // Diğer tüm misafirlerin ana misafir statüsünü kaldır
                            $livewire->ownerRecord->guests()
                                ->where('id', '!=', $livewire->getOwnerRecord()->id)
                                ->update(['is_primary' => false]);
                        }
                    }),
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
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $birthDate = \Carbon\Carbon::parse($state);
                            $age = $birthDate->age;
                            $set('age', $age);
                            
                            // Misafir tipini otomatik belirle
                            if ($age >= 18) {
                                $set('guest_type', 'adult');
                            } elseif ($age >= 2) {
                                $set('guest_type', 'child');
                            } else {
                                $set('guest_type', 'infant');
                            }
                        }
                    }),
                Forms\Components\TextInput::make('age')
                    ->label('Yaş')
                    ->numeric()
                    ->minValue(0)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Select::make('guest_type')
                    ->label('Misafir Tipi')
                    ->options([
                        'adult' => 'Yetişkin',
                        'child' => 'Çocuk',
                        'infant' => 'Bebek',
                    ])
                    ->required()
                    ->default('adult'),
                Forms\Components\TextInput::make('id_number')
                    ->label('Kimlik Numarası')
                    ->maxLength(255),
                Forms\Components\Select::make('id_type')
                    ->label('Kimlik Tipi')
                    ->options([
                        'id_card' => 'Kimlik Kartı',
                        'passport' => 'Pasaport',
                        'driving_license' => 'Ehliyet',
                        'other' => 'Diğer',
                    ])
                    ->default('id_card'),
                Forms\Components\TextInput::make('nationality')
                    ->label('Uyruk')
                    ->default('TC')
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->label('Cinsiyet')
                    ->options([
                        'male' => 'Erkek',
                        'female' => 'Kadın',
                        'other' => 'Diğer',
                    ]),
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
                    ->default('Türkiye')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_primary')
                    ->label('Ana Misafir')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Ad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Soyad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_type')
                    ->label('Tipi')
                    ->badge()
                    ->color(function ($state) {
                        if ($state === null) {
                            return 'gray';
                        }
                        
                        return match ($state) {
                            'adult' => 'primary',
                            'child' => 'warning',
                            'infant' => 'info',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'Belirtilmemiş';
                        }
                        
                        return match ($state) {
                            'adult' => 'Yetişkin',
                            'child' => 'Çocuk',
                            'infant' => 'Bebek',
                            default => (string)$state,
                        };
                    }),
                Tables\Columns\TextColumn::make('age')
                    ->label('Yaş')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_number')
                    ->label('Kimlik No')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->label('Uyruk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Cinsiyet')
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'Belirtilmemiş';
                        }
                        
                        return match ($state) {
                            'male' => 'Erkek',
                            'female' => 'Kadın',
                            'other' => 'Diğer',
                            default => (string)$state,
                        };
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guest_type')
                    ->label('Misafir Tipi')
                    ->options([
                        'adult' => 'Yetişkin',
                        'child' => 'Çocuk',
                        'infant' => 'Bebek',
                    ]),
                Tables\Filters\TernaryFilter::make('is_primary')
                    ->label('Ana Misafir'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        // Eğer bu ilk misafirse otomatik olarak ana misafir yap
                        if ($this->ownerRecord->guests()->count() === 0) {
                            $data['is_primary'] = true;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.guests.view', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('make_primary')
                    ->label('Ana Misafir Yap')
                    ->icon('heroicon-o-star')
                    ->color('success')
                    ->visible(fn ($record) => !$record->is_primary)
                    ->action(function ($record) {
                        // Mevcut ana misafiri güncelle
                        $this->ownerRecord->guests()
                            ->where('is_primary', true)
                            ->update(['is_primary' => false]);
                            
                        // Yeni ana misafiri ayarla
                        $record->update(['is_primary' => true]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}