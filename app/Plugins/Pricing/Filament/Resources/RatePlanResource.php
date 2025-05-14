<?php

namespace App\Plugins\Pricing\Filament\Resources;

use App\Plugins\Pricing\Filament\Resources\RatePlanResource\Pages;
use App\Plugins\Pricing\Models\RatePlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RatePlanResource extends Resource
{
    protected static ?string $model = RatePlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Oda Yönetimi';
    protected static ?string $navigationLabel = 'Tarife Planları';
    protected static ?string $modelLabel = 'Tarife Planı';
    protected static ?string $pluralModelLabel = 'Tarife Planları';
    protected static ?int $navigationSort = 5;
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tarife Planı')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'vertical-tabs'])
                    ->tabs([
                        // Temel Bilgiler Sekmesi
                        Forms\Components\Tabs\Tab::make('Temel Bilgiler')
                            ->icon('heroicon-o-information-circle')
                            ->badge(fn () => 'Genel')
                            ->schema([
                                Forms\Components\Section::make('Tarife Planı Bilgileri')
                                    ->description('Tarife planının temel bilgilerini giriniz')
                                    ->icon('heroicon-o-document-text')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Card::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Plan Adı')
                                                    ->required()
                                                    ->placeholder('Örn: Standart Tarife Plan')
                                                    ->helperText('Tarife planının adı')
                                                    ->maxLength(255)
                                                    ->columnSpan(2),
                                                    
                                                Forms\Components\Select::make('hotel_id')
                                                    ->label('Otel')
                                                    ->relationship('hotel', 'name')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->columnSpan(2),
                                                    
                                                Forms\Components\Select::make('room_id')
                                                    ->label('Oda')
                                                    ->relationship('room', 'name', function (Builder $query, callable $get) {
                                                        $hotelId = $get('hotel_id');
                                                        if ($hotelId) {
                                                            $query->where('hotel_id', $hotelId);
                                                        }
                                                        return $query;
                                                    })
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Oda seçin (opsiyonel)')
                                                    ->helperText('Belirli bir oda için geçerli ise seçin')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Select::make('room_type_id')
                                                    ->label('Oda Tipi')
                                                    ->relationship('roomType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->placeholder('Oda tipi seçin (opsiyonel)')
                                                    ->helperText('Belirli bir oda tipi için geçerli ise seçin')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\RichEditor::make('description')
                                                    ->label('Açıklama')
                                                    ->placeholder('Tarife planı hakkında açıklama yazın...')
                                                    ->toolbarButtons([
                                                        'bold',
                                                        'italic',
                                                        'bulletList',
                                                        'orderedList',
                                                    ])
                                                    ->columnSpan(4),
                                            ])
                                            ->columns(4),
                                    ]),
                                    
                                Forms\Components\Section::make('Fiyat ve Kontenjan Türü')
                                    ->description('Fiyatlandırma ve kontenjan ayarları')
                                    ->icon('heroicon-o-banknotes')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Toggle::make('occupancy_pricing')
                                                            ->label('Kişi Başı Fiyatlandırma')
                                                            ->helperText('Kişi sayısına göre fiyatlandırma yapılacaksa aktif edin')
                                                            ->onIcon('heroicon-s-user-group')
                                                            ->offIcon('heroicon-s-home')
                                                            ->default(false)
                                                            ->reactive()
                                                            ->afterStateUpdated(fn (Forms\Set $set, $state) => $set('occupancy_pricing_description', $state ? 'Kişi başı fiyatlandırma aktif' : 'Birim (oda) bazlı fiyatlandırma aktif')),
                                                        
                                                        Forms\Components\Placeholder::make('occupancy_pricing_description')
                                                            ->label('Fiyatlandırma Türü Açıklaması')
                                                            ->content(fn (callable $get) => $get('occupancy_pricing') 
                                                                ? 'Kişi başı fiyatlandırma aktif. Her kişi sayısı için farklı fiyat belirleyebilirsiniz.'
                                                                : 'Birim (oda) bazlı fiyatlandırma aktif. Odanın toplam fiyatı belirlenecek.'),
                                                    ])
                                                    ->heading('Fiyatlandırma Türü')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-primary-500'])
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('meal_plan')
                                                            ->label('Pansiyon Tipi')
                                                            ->options([
                                                                'none' => 'Pansiyon Yok (Sadece Oda)',
                                                                'breakfast' => 'Kahvaltı Dahil',
                                                                'half_board' => 'Yarım Pansiyon',
                                                                'full_board' => 'Tam Pansiyon',
                                                                'all_inclusive' => 'Her Şey Dahil',
                                                            ])
                                                            ->default('none')
                                                            ->helperText('Bu tarife planındaki konaklamaya dahil olan yemekler'),
                                                            
                                                        Forms\Components\Select::make('payment_type')
                                                            ->label('Ödeme Türü')
                                                            ->options([
                                                                'pay_online' => 'Online Öde',
                                                                'reserve_only' => 'Rezerve Et',
                                                                'inquire_only' => 'Sorun (Fiyat alın)',
                                                            ])
                                                            ->default('reserve_only')
                                                            ->helperText('Bu tarife planı için ödeme şekli'),
                                                    ])
                                                    ->heading('Ödeme & Pansiyon')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-warning-500'])
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                                    
                                Forms\Components\Section::make('Minimum Kalış ve Kısıtlamalar')
                                    ->description('Kalış süresi ve diğer kısıtlamalar')
                                    ->icon('heroicon-o-clock')
                                    ->aside()
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('min_stay')
                                                            ->label('Minimum Kalış')
                                                            ->helperText('Minimum kaç gece konaklanmalı')
                                                            ->numeric()
                                                            ->default(1)
                                                            ->minValue(1),
                                                        
                                                        Forms\Components\TextInput::make('max_stay')
                                                            ->label('Maksimum Kalış')
                                                            ->helperText('Maksimum kaç gece konaklanabilir (opsiyonel)')
                                                            ->numeric()
                                                            ->minValue(1)
                                                            ->placeholder('Sınırsız'),
                                                    ])
                                                    ->heading('Kalış Süresi')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-info-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\CheckboxList::make('restriction_days')
                                                            ->label('Kısıtlı Günler')
                                                            ->helperText('Bu plana izin verilmeyen haftanın günleri')
                                                            ->options([
                                                                1 => 'Pazartesi',
                                                                2 => 'Salı',
                                                                3 => 'Çarşamba',
                                                                4 => 'Perşembe',
                                                                5 => 'Cuma',
                                                                6 => 'Cumartesi',
                                                                7 => 'Pazar',
                                                            ]),
                                                    ])
                                                    ->heading('Kısıtlamalar')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-danger-500'])
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                                
                                Forms\Components\Section::make('İptal Politikası')
                                    ->description('Rezervasyon iptal kuralları')
                                    ->icon('heroicon-o-x-circle')
                                    ->aside()
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\RichEditor::make('cancellation_policy')
                                            ->label('İptal Politikası')
                                            ->placeholder('İptal politikasını detaylı olarak açıklayın...')
                                            ->helperText('Bu tarife planı için geçerli olan iptal kuralları')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'bulletList',
                                                'orderedList',
                                            ]),
                                    ]),
                                
                                Forms\Components\Section::make('Durum Bilgileri')
                                    ->description('Tarife planının yayınlanma ve sıralama ayarları')
                                    ->icon('heroicon-o-check-circle')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Toggle::make('is_active')
                                                            ->label('Aktif')
                                                            ->helperText('Aktif olduğunda müşterilere gösterilir')
                                                            ->default(true)
                                                            ->onIcon('heroicon-s-check')
                                                            ->offIcon('heroicon-s-x-mark'),
                                                    ])
                                                    ->heading('Aktiflik Durumu')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-success-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Toggle::make('is_default')
                                                            ->label('Varsayılan Plan')
                                                            ->helperText('Bu oda için varsayılan tarife planı')
                                                            ->default(false)
                                                            ->onIcon('heroicon-s-star')
                                                            ->offIcon('heroicon-s-x-mark'),
                                                    ])
                                                    ->heading('Varsayılan')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-warning-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('sort_order')
                                                            ->label('Sıralama')
                                                            ->helperText('Planların listede gösterilme sırası')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->minValue(0),
                                                    ])
                                                    ->heading('Sıralama')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-info-500'])
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),
                            
                        // Çocuk Politikaları Sekmesi    
                        Forms\Components\Tabs\Tab::make('Çocuk Politikaları')
                            ->icon('heroicon-o-face-smile')
                            ->badge(fn () => 'Çocuk Fiyatları')
                            ->schema([
                                Forms\Components\Section::make('Çocuk Yaş Politikaları')
                                    ->description('Çocuklar için yaşa göre fiyatlandırma kurallarını belirleyin')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        Forms\Components\Repeater::make('childPolicies')
                                            ->relationship()
                                            ->label('Çocuk Yaş Grupları')
                                            ->helperText('Her yaş grubu için fiyatlandırma politikasını belirleyin')
                                            ->schema([
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\Card::make()
                                                            ->schema([
                                                                Forms\Components\TextInput::make('min_age')
                                                                    ->label('Minimum Yaş')
                                                                    ->placeholder('Örn: 0')
                                                                    ->helperText('Alt yaş sınırı')
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->required()
                                                                    ->suffixIcon('heroicon-o-minus-small'),
                                                                    
                                                                Forms\Components\TextInput::make('max_age')
                                                                    ->label('Maksimum Yaş')
                                                                    ->placeholder('Örn: 6')
                                                                    ->helperText('Üst yaş sınırı')
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->required()
                                                                    ->suffixIcon('heroicon-o-plus-small'),
                                                            ])
                                                            ->heading('Yaş Aralığı')
                                                            ->extraAttributes(['class' => 'border-l-4 border-l-primary-500'])
                                                            ->columnSpan(1),
                                                            
                                                        Forms\Components\Card::make()
                                                            ->schema([
                                                                Forms\Components\Select::make('policy_type')
                                                                    ->label('Politika Tipi')
                                                                    ->helperText('Bu yaş grubu için fiyat politikası')
                                                                    ->options([
                                                                        'free' => '🎁 Ücretsiz',
                                                                        'fixed_price' => '💰 Sabit Fiyat',
                                                                        'percentage' => '🏷️ Yüzde İndirim',
                                                                    ])
                                                                    ->default('free')
                                                                    ->required()
                                                                    ->reactive(),
                                                                
                                                                Forms\Components\Grid::make()
                                                                    ->schema([
                                                                        Forms\Components\TextInput::make('amount')
                                                                            ->label(fn (callable $get) => match($get('policy_type')) {
                                                                                'fixed_price' => 'Sabit Fiyat',
                                                                                'percentage' => 'İndirim Yüzdesi',
                                                                                default => 'Değer',
                                                                            })
                                                                            ->placeholder('Örn: 100')
                                                                            ->numeric()
                                                                            ->required()
                                                                            ->visible(fn (callable $get) => $get('policy_type') !== 'free')
                                                                            ->minValue(0),
                                                                            
                                                                        Forms\Components\Select::make('currency')
                                                                            ->label('Para Birimi')
                                                                            ->options([
                                                                                'TRY' => '₺ TL',
                                                                                'USD' => '$ USD',
                                                                                'EUR' => '€ EUR',
                                                                                'GBP' => '£ GBP',
                                                                            ])
                                                                            ->default('TRY')
                                                                            ->visible(fn (callable $get) => $get('policy_type') === 'fixed_price'),
                                                                    ])
                                                                    ->columns(2),
                                                            ])
                                                            ->heading('Fiyatlandırma')
                                                            ->extraAttributes(['class' => 'border-l-4 border-l-warning-500'])
                                                            ->columnSpan(1),
                                                            
                                                        Forms\Components\Card::make()
                                                            ->schema([
                                                                Forms\Components\TextInput::make('child_number')
                                                                    ->label('Çocuk Sırası')
                                                                    ->helperText('1 ise 1. çocuk, 2 ise 2. çocuk vb.')
                                                                    ->numeric()
                                                                    ->minValue(1)
                                                                    ->default(1)
                                                                    ->required(),
                                                                
                                                                Forms\Components\TextInput::make('max_children')
                                                                    ->label('Maksimum Çocuk')
                                                                    ->helperText('Bu politika kapsamında kabul edilecek max çocuk sayısı')
                                                                    ->numeric()
                                                                    ->minValue(1)
                                                                    ->default(1)
                                                                    ->required(),
                                                            ])
                                                            ->heading('Kapasite')
                                                            ->extraAttributes(['class' => 'border-l-4 border-l-success-500'])
                                                            ->columnSpan(1),
                                                    ])
                                                    ->columns(3),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => 
                                                ($state['min_age'] ?? '?') . '-' . ($state['max_age'] ?? '?') . ' yaş: ' . 
                                                match($state['policy_type'] ?? 'free') {
                                                    'free' => 'Ücretsiz',
                                                    'fixed_price' => $state['amount'] ?? '0' . ' ' . ($state['currency'] ?? 'TRY'),
                                                    'percentage' => '%' . ($state['amount'] ?? '0') . ' indirim',
                                                    default => 'Belirtilmemiş'
                                                } . ' (Çocuk #' . ($state['child_number'] ?? '1') . ')'
                                            )
                                            ->collapsible()
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),
                            
                        // Fiyat ve Kapasite Yönetimi (İleri düzey) sekmesi gelecek...
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Tarife Planı')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('hotel.name')
                    ->label('Otel')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Oda')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('roomType.name')
                    ->label('Oda Tipi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\BadgeColumn::make('occupancy_pricing')
                    ->label('Fiyatlama')
                    ->formatStateUsing(function ($state): string { 
                        if ($state === null) {
                            return 'Belirtilmemiş';
                        }
                        return (bool)$state ? 'Kişi Başı' : 'Birim (Oda)';
                    })
                    ->colors([
                        'primary' => fn ($state): bool => $state === true, // Kişi Başı
                        'warning' => fn ($state): bool => $state === false, // Birim (Oda)
                        'gray' => fn ($state): bool => $state === null, // Belirtilmemiş
                    ]),
                    
                Tables\Columns\TextColumn::make('meal_plan')
                    ->label('Pansiyon')
                    ->formatStateUsing(function ($state): string {
                        if ($state === null) {
                            return 'Belirtilmemiş';
                        }
                        
                        return match($state) {
                            'none' => 'Sadece Oda',
                            'breakfast' => 'Kahvaltı Dahil',
                            'half_board' => 'Yarım Pansiyon',
                            'full_board' => 'Tam Pansiyon',
                            'all_inclusive' => 'Her Şey Dahil',
                            default => (string)$state,
                        };
                    })
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('min_stay')
                    ->label('Min. Kalış')
                    ->formatStateUsing(function ($state): string {
                        if ($state === null) {
                            return 'Belirtilmemiş';
                        }
                        return (string)$state . ' gece';
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Varsayılan')
                    ->boolean()
                    ->sortable(),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('hotel_id')
                    ->label('Otel')
                    ->relationship('hotel', 'name'),
                
                Tables\Filters\SelectFilter::make('room_type_id')
                    ->label('Oda Tipi')
                    ->relationship('roomType', 'name'),
                    
                Tables\Filters\SelectFilter::make('meal_plan')
                    ->label('Pansiyon Tipi')
                    ->options([
                        'none' => 'Sadece Oda',
                        'breakfast' => 'Kahvaltı Dahil',
                        'half_board' => 'Yarım Pansiyon',
                        'full_board' => 'Tam Pansiyon',
                        'all_inclusive' => 'Her Şey Dahil',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('occupancy_pricing')
                    ->label('Fiyatlama Türü')
                    ->placeholder('Tümü')
                    ->trueLabel('Kişi Başı')
                    ->falseLabel('Birim (Oda)'),
                    
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Varsayılan'),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('manage_prices')
                        ->label('Fiyatları Yönet')
                        ->icon('heroicon-o-banknotes')
                        ->url(fn (Model $record): string => static::getUrl('manage-prices', ['record' => $record]))
                        ->color('success'),
                        
                    Tables\Actions\Action::make('manage_inventory')
                        ->label('Kontenjan Yönet')
                        ->icon('heroicon-o-calendar')
                        ->url(fn (Model $record): string => static::getUrl('manage-inventory', ['record' => $record]))
                        ->color('warning'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktif Et')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => true])),
                        
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Pasif Et')
                        ->color('danger')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => false])),
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
            'index' => Pages\ListRatePlans::route('/'),
            'create' => Pages\CreateRatePlan::route('/create'),
            'edit' => Pages\EditRatePlan::route('/{record}/edit'),
            'manage-prices' => Pages\ManageRatePlanPrices::route('/{record}/prices'),
            'manage-inventory' => Pages\ManageRatePlanInventory::route('/{record}/inventory'),
        ];
    }
}