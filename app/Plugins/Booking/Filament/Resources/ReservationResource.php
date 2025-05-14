<?php

namespace App\Plugins\Booking\Filament\Resources;

use App\Plugins\Booking\Filament\Resources\ReservationResource\Pages;
use App\Plugins\Booking\Filament\Resources\ReservationResource\RelationManagers;
use App\Plugins\Booking\Models\Reservation;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Room;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $modelLabel = 'Rezervasyon';
    protected static ?string $pluralModelLabel = 'Rezervasyonlar';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Rezervasyon Yönetimi';
    
    protected static ?int $navigationSort = 1;
    
    public static function canAccess(): bool
    {
        return true; // Geçici olarak herkesin erişimine izin ver
    }
    
    public static function canCreate(): bool
    {
        return true; // Geçici olarak herkesin oluşturmasına izin ver
    }
    
    public static function canEdit(Model $record): bool
    {
        return true; // Geçici olarak herkesin düzenlemesine izin ver
    }
    
    public static function canDelete(Model $record): bool
    {
        return true; // Geçici olarak herkesin silmesine izin ver
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Rezervasyon Bilgileri')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'vertical-tabs'])
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Temel Bilgiler')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('reservation_number')
                                    ->label('Rezervasyon Numarası')
                                    ->default('RES-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                                Forms\Components\Select::make('hotel_id')
                                    ->label('Otel')
                                    ->relationship('hotel', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set) => $set('room_id', null)),
                                Forms\Components\Select::make('room_id')
                                    ->label('Oda')
                                    ->options(function (callable $get) {
                                        $hotelId = $get('hotel_id');
                                        if (!$hotelId) {
                                            return [];
                                        }
                                        
                                        return Room::where('hotel_id', $hotelId)
                                            ->where('is_active', true)
                                            ->where('is_available', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('user_id')
                                    ->label('Kullanıcı')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Tarih & Konuklar')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Forms\Components\DatePicker::make('check_in')
                                    ->label('Giriş Tarihi')
                                    ->required()
                                    ->minDate(now())
                                    ->native(false)
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $checkIn = $get('check_in');
                                        $checkOut = $get('check_out');
                                        
                                        if ($checkIn && $checkOut) {
                                            $checkIn = Carbon::parse($checkIn);
                                            $checkOut = Carbon::parse($checkOut);
                                            
                                            if ($checkOut->lessThanOrEqualTo($checkIn)) {
                                                $newCheckOut = $checkIn->copy()->addDay();
                                                $set('check_out', $newCheckOut);
                                            }
                                            
                                            // Gece sayısını hesapla
                                            $nights = $checkIn->diffInDays($checkOut);
                                            $set('nights', $nights);
                                        }
                                    }),
                                Forms\Components\DatePicker::make('check_out')
                                    ->label('Çıkış Tarihi')
                                    ->required()
                                    ->native(false)
                                    ->minDate(function (callable $get) {
                                        $checkIn = $get('check_in');
                                        if ($checkIn) {
                                            return Carbon::parse($checkIn)->addDay();
                                        }
                                        return now()->addDay();
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $checkIn = $get('check_in');
                                        $checkOut = $get('check_out');
                                        
                                        if ($checkIn && $checkOut) {
                                            // Gece sayısını hesapla
                                            $checkIn = Carbon::parse($checkIn);
                                            $checkOut = Carbon::parse($checkOut);
                                            $nights = $checkIn->diffInDays($checkOut);
                                            $set('nights', $nights);
                                        }
                                    }),
                                Forms\Components\TextInput::make('nights')
                                    ->label('Gece Sayısı')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(1),
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
                                Forms\Components\TextInput::make('infants')
                                    ->label('Bebek Sayısı')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('İrtibat Bilgileri')
                            ->icon('heroicon-o-user')
                            ->schema([
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
                                Forms\Components\Textarea::make('special_requests')
                                    ->label('Özel İstekler')
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Fiyatlandırma')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('room_price')
                                    ->label('Oda Fiyatı')
                                    ->numeric()
                                    ->reactive()
                                    ->step(0.01)
                                    ->required()
                                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                                        $set('total_price', 
                                            ($get('room_price') ?? 0) + 
                                            ($get('extras_price') ?? 0) - 
                                            ($get('discount') ?? 0) + 
                                            ($get('taxes') ?? 0)
                                        )
                                    ),
                                Forms\Components\TextInput::make('extras_price')
                                    ->label('Ekstra Hizmetler')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                                        $set('total_price', 
                                            ($get('room_price') ?? 0) + 
                                            ($get('extras_price') ?? 0) - 
                                            ($get('discount') ?? 0) + 
                                            ($get('taxes') ?? 0)
                                        )
                                    ),
                                Forms\Components\TextInput::make('discount')
                                    ->label('İndirim')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                                        $set('total_price', 
                                            ($get('room_price') ?? 0) + 
                                            ($get('extras_price') ?? 0) - 
                                            ($get('discount') ?? 0) + 
                                            ($get('taxes') ?? 0)
                                        )
                                    ),
                                Forms\Components\TextInput::make('taxes')
                                    ->label('Vergiler')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                                        $set('total_price', 
                                            ($get('room_price') ?? 0) + 
                                            ($get('extras_price') ?? 0) - 
                                            ($get('discount') ?? 0) + 
                                            ($get('taxes') ?? 0)
                                        )
                                    ),
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Toplam Fiyat')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                                        $set('balance_due', 
                                            ($get('total_price') ?? 0) - 
                                            ($get('amount_paid') ?? 0)
                                        )
                                    ),
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
                                Forms\Components\Select::make('payment_status')
                                    ->label('Ödeme Durumu')
                                    ->options([
                                        'pending' => 'Beklemede',
                                        'partially_paid' => 'Kısmen Ödenmiş',
                                        'paid' => 'Ödenmiş',
                                        'refunded' => 'İade Edilmiş',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                Forms\Components\TextInput::make('amount_paid')
                                    ->label('Ödenen Miktar')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->reactive()
                                    ->afterStateUpdated(fn (callable $set, callable $get) => 
                                        $set('balance_due', 
                                            ($get('total_price') ?? 0) - 
                                            ($get('amount_paid') ?? 0)
                                        )
                                    ),
                                Forms\Components\TextInput::make('balance_due')
                                    ->label('Kalan Miktar')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->disabled()
                                    ->dehydrated(),
                                Forms\Components\Select::make('payment_method')
                                    ->label('Ödeme Yöntemi')
                                    ->options([
                                        'cash' => 'Nakit',
                                        'credit_card' => 'Kredi Kartı',
                                        'bank_transfer' => 'Banka Transferi',
                                        'paypal' => 'PayPal',
                                        'other' => 'Diğer',
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Durum & Notlar')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
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
                                    ->default('pending')
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                        if ($state === 'confirmed' && !$get('confirmed_at')) {
                                            $set('confirmed_at', now());
                                        } else if ($state === 'cancelled' && !$get('cancelled_at')) {
                                            $set('cancelled_at', now());
                                        }
                                    }),
                                Forms\Components\DateTimePicker::make('confirmed_at')
                                    ->label('Onay Tarihi')
                                    ->native(false)
                                    ->visible(fn (callable $get) => in_array($get('status'), ['confirmed', 'checked_in', 'checked_out'])),
                                Forms\Components\DateTimePicker::make('cancelled_at')
                                    ->label('İptal Tarihi')
                                    ->native(false)
                                    ->visible(fn (callable $get) => $get('status') === 'cancelled'),
                                Forms\Components\TextInput::make('cancellation_reason')
                                    ->label('İptal Nedeni')
                                    ->maxLength(255)
                                    ->visible(fn (callable $get) => $get('status') === 'cancelled'),
                                Forms\Components\Select::make('source')
                                    ->label('Rezervasyon Kaynağı')
                                    ->options([
                                        'direct' => 'Direkt',
                                        'website' => 'Web Sitesi',
                                        'booking' => 'Booking.com',
                                        'expedia' => 'Expedia',
                                        'airbnb' => 'AirBnB',
                                        'phone' => 'Telefon',
                                        'email' => 'E-posta',
                                        'walk_in' => 'Walk-in',
                                        'agent' => 'Acente',
                                        'other' => 'Diğer',
                                    ])
                                    ->default('direct'),
                                Forms\Components\TextInput::make('agency_id')
                                    ->label('Acente ID')
                                    ->visible(fn (callable $get) => $get('source') === 'agent'),
                                Forms\Components\Textarea::make('notes')
                                    ->label('Müşteri Notları')
                                    ->maxLength(65535),
                                Forms\Components\Textarea::make('admin_notes')
                                    ->label('Yönetici Notları')
                                    ->maxLength(65535),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reservation_number')
                    ->label('Rezervasyon No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Otel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Oda')
                    ->searchable()
                    ->sortable(),
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
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Ödeme Durumu')
                    ->badge()
                    ->color(function ($state) {
                        if ($state === null) {
                            return 'gray';
                        }
                        
                        return match ($state) {
                            'pending' => 'gray',
                            'partially_paid' => 'warning',
                            'paid' => 'success',
                            'refunded' => 'danger',
                            default => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state === null) {
                            return 'Belirtilmemiş';
                        }
                        
                        return match ($state) {
                            'pending' => 'Beklemede',
                            'partially_paid' => 'Kısmen Ödenmiş',
                            'paid' => 'Ödenmiş',
                            'refunded' => 'İade Edilmiş',
                            default => (string)$state,
                        };
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hotel_id')
                    ->label('Otel')
                    ->relationship('hotel', 'name'),
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
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Ödeme Durumu')
                    ->options([
                        'pending' => 'Beklemede',
                        'partially_paid' => 'Kısmen Ödenmiş',
                        'paid' => 'Ödenmiş',
                        'refunded' => 'İade Edilmiş',
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Model $record) => $record->status === 'pending')
                    ->action(function (Model $record) {
                        $record->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('İptal Et')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Model $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->form([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('İptal Nedeni')
                            ->required(),
                    ])
                    ->action(function (array $data, Model $record) {
                        $record->update([
                            'status' => 'cancelled',
                            'cancelled_at' => now(),
                            'cancellation_reason' => $data['cancellation_reason'],
                        ]);
                    }),
                Tables\Actions\Action::make('check_in')
                    ->label('Giriş Yap')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('info')
                    ->visible(fn (Model $record) => $record->status === 'confirmed')
                    ->action(function (Model $record) {
                        $record->update([
                            'status' => 'checked_in',
                        ]);
                    }),
                Tables\Actions\Action::make('check_out')
                    ->label('Çıkış Yap')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('warning')
                    ->visible(fn (Model $record) => $record->status === 'checked_in')
                    ->action(function (Model $record) {
                        $record->update([
                            'status' => 'checked_out',
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('confirm_reservations')
                        ->label('Toplu Onay')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->update([
                                        'status' => 'confirmed',
                                        'confirmed_at' => now(),
                                    ]);
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\GuestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}