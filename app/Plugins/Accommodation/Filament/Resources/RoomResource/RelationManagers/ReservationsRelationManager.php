<?php

namespace App\Plugins\Accommodation\Filament\Resources\RoomResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';

    protected static ?string $recordTitleAttribute = 'reservation_number';

    protected static ?string $title = 'Rezervasyonlar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('reservation_number')
                    ->label('Rezervasyon Numarası')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->unique(ignoreRecord: true),
                Forms\Components\DatePicker::make('check_in')
                    ->label('Giriş Tarihi')
                    ->native(false)
                    ->required(),
                Forms\Components\DatePicker::make('check_out')
                    ->label('Çıkış Tarihi')
                    ->native(false)
                    ->required()
                    ->after('check_in'),
                Forms\Components\TextInput::make('adults')
                    ->label('Yetişkin Sayısı')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Forms\Components\TextInput::make('children')
                    ->label('Çocuk Sayısı')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                Forms\Components\TextInput::make('contact_name')
                    ->label('İrtibat Kişisi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_email')
                    ->label('E-posta')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('contact_phone')
                    ->label('Telefon')
                    ->tel()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'confirmed' => 'Onaylandı',
                        'checked_in' => 'Giriş Yapıldı',
                        'checked_out' => 'Çıkış Yapıldı',
                        'cancelled' => 'İptal Edildi',
                        'no_show' => 'Gelmedi',
                    ])
                    ->required()
                    ->default('pending'),
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
                Tables\Columns\TextColumn::make('reservation_number')
                    ->label('Rezervasyon No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Giriş')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Çıkış')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nights')
                    ->label('Gece')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('adults')
                    ->label('Yetişkin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('children')
                    ->label('Çocuk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('İrtibat Kişisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Toplam Fiyat')
                    ->money(fn($record) => $record->currency)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(function ($state) {
                        if ($state === null) {
                            return 'gray';
                        }
                        
                        return match ($state) {
                            'pending' => 'gray',
                            'confirmed' => 'info',
                            'checked_in' => 'success',
                            'checked_out' => 'primary',
                            'cancelled' => 'danger',
                            'no_show' => 'warning',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'Belirtilmemiş';
                        }
                        
                        return match ($state) {
                            'pending' => 'Beklemede',
                            'confirmed' => 'Onaylandı',
                            'checked_in' => 'Giriş Yapıldı',
                            'checked_out' => 'Çıkış Yapıldı',
                            'cancelled' => 'İptal Edildi',
                            'no_show' => 'Gelmedi',
                            default => (string)$state,
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Beklemede',
                        'confirmed' => 'Onaylandı',
                        'checked_in' => 'Giriş Yapıldı',
                        'checked_out' => 'Çıkış Yapıldı',
                        'cancelled' => 'İptal Edildi',
                        'no_show' => 'Gelmedi',
                    ]),
                Tables\Filters\Filter::make('check_in')
                    ->label('Giriş Tarihi')
                    ->form([
                        Forms\Components\DatePicker::make('check_in_from')
                            ->label('Başlangıç')
                            ->native(false),
                        Forms\Components\DatePicker::make('check_in_until')
                            ->label('Bitiş')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['check_in_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('check_in', '>=', $date),
                            )
                            ->when(
                                $data['check_in_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('check_in', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}