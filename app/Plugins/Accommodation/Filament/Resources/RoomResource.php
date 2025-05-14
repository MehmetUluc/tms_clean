<?php

namespace App\Plugins\Accommodation\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\RoomResource\Pages;
use App\Plugins\Accommodation\Filament\Resources\RoomResource\RelationManagers;
use App\Plugins\Accommodation\Models\Room;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\RoomType;
use App\Plugins\Amenities\Models\RoomAmenity;
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
use Illuminate\Database\Eloquent\Collection;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $modelLabel = 'Oda';
    protected static ?string $pluralModelLabel = 'Odalar';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Oda Yönetimi';

    protected static ?int $navigationSort = 2;

    /**
     * Vendor kullanıcıları için navigasyon etiketlerini özelleştirelim
     */
    public static function getNavigationLabel(): string
    {
        // Vendor için daha açıklayıcı bir etiket
        if (auth()->check() && auth()->user()->hasRole('vendor')) {
            return 'Odalarım';
        }

        // Varsayılan etiket
        return static::$pluralModelLabel ?? static::getPluralModelLabel();
    }

    /**
     * Vendor kullanıcıları için navigasyon grubunu özelleştirelim
     */
    public static function getNavigationGroup(): ?string
    {
        // Vendor için farklı navigasyon grubu
        if (auth()->check() && auth()->user()->hasRole('vendor')) {
            return 'Otel Yönetimi';
        }

        // Varsayılan grup
        return static::$navigationGroup;
    }

    /**
     * Vendor kullanıcıları için navigasyon sıralamasını özelleştirelim
     */
    public static function getNavigationSort(): ?int
    {
        // Vendor için farklı navigasyon sıralaması
        if (auth()->check() && auth()->user()->hasRole('vendor')) {
            return 20;
        }

        // Varsayılan sıralama
        return static::$navigationSort;
    }
    
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
                Forms\Components\Tabs::make('Oda Bilgileri')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'vertical-tabs'])
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Temel Bilgiler')
                            ->icon('heroicon-o-information-circle')
                            ->badge(fn () => 'Kimlik')
                            ->schema([
                                Forms\Components\Section::make('Oda Tanımlama Bilgileri')
                                    ->description('Odanın temel tanımlama bilgilerini burada girebilirsiniz')
                                    ->icon('heroicon-o-identification')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Select::make('hotel_id')
                                                    ->label('Bağlı Olduğu Otel')
                                                    ->helperText('Odanın hangi otele ait olduğunu seçin')
                                                    ->relationship('hotel', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        $name = $get('name');
                                                        if ($name && $state) {
                                                            $baseSlug = \Illuminate\Support\Str::slug($name);
                                                            $set('slug', $state . '-' . $baseSlug);
                                                        }
                                                    })
                                                    ->columnSpan(2),
                                                
                                                Forms\Components\Select::make('room_type_id')
                                                    ->label('Oda Tipi')
                                                    ->helperText('Odanın tipini seçin')
                                                    ->relationship('roomType', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->columnSpan(2),
                                            ])
                                            ->columns(4),
                                        
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Oda Adı')
                                                            ->placeholder('Örn: Deluxe Oda 101')
                                                            ->helperText('Odanın tam adını girin')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function (string $state, callable $set, callable $get) {
                                                                $hotelId = $get('hotel_id');
                                                                $baseSlug = \Illuminate\Support\Str::slug($state);

                                                                // Otel ID'si varsa, onu slug'a dahil et
                                                                if ($hotelId) {
                                                                    $set('slug', $hotelId . '-' . $baseSlug);
                                                                } else {
                                                                    $set('slug', $baseSlug);
                                                                }
                                                            }),

                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('URL Adresi')
                                                            ->placeholder('Örn: 1-deluxe-oda-101')
                                                            ->helperText('Web adresinde görünecek kısa ad (otomatik oluşturulur - otel id değişirse güncellenir)')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique(ignoreRecord: true),
                                                    ])
                                                    ->columns(1)
                                                    ->columnSpan(2),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('room_number')
                                                            ->label('Oda Numarası')
                                                            ->placeholder('Örn: 101')
                                                            ->helperText('Oda numarası veya kodu')
                                                            ->maxLength(50),
                                                            
                                                        Forms\Components\TextInput::make('floor')
                                                            ->label('Kat')
                                                            ->placeholder('Örn: 1')
                                                            ->helperText('Odanın bulunduğu kat')
                                                            ->numeric()
                                                            ->minValue(0),
                                                    ])
                                                    ->columns(1)
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Kapasite & Fiyat')
                            ->icon('heroicon-o-user-group')
                            ->badge(fn () => 'Özellikler')
                            ->schema([
                                Forms\Components\Section::make('Kapasite Bilgileri')
                                    ->description('Odanın maksimum misafir kapasitesini belirleyin')
                                    ->icon('heroicon-o-user-group')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('capacity_adults')
                                                            ->label('Yetişkin Kapasitesi')
                                                            ->helperText('Maksimum yetişkin sayısı')
                                                            ->placeholder('Örn: 2')
                                                            ->numeric()
                                                            ->default(2)
                                                            ->minValue(1)
                                                            ->required()
                                                            ->suffixIcon('heroicon-o-user'),
                                                    ])
                                                    ->heading('Yetişkin')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-primary-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('capacity_children')
                                                            ->label('Çocuk Kapasitesi')
                                                            ->helperText('Maksimum çocuk sayısı')
                                                            ->placeholder('Örn: 1')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->required()
                                                            ->suffixIcon('heroicon-o-face-smile'),
                                                    ])
                                                    ->heading('Çocuk')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-warning-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('size')
                                                            ->label('Metrekare')
                                                            ->helperText('Odanın büyüklüğü')
                                                            ->placeholder('Örn: 25')
                                                            ->numeric()
                                                            ->step(0.01)
                                                            ->suffix('m²')
                                                            ->suffixIcon('heroicon-o-square-2-stack'),
                                                    ])
                                                    ->heading('Büyüklük')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-success-500'])
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                                    
                                Forms\Components\Section::make('Fiyat Bilgileri')
                                    ->description('Odanın fiyatlandırma hesaplama yöntemini belirleyin')
                                    ->icon('heroicon-o-banknotes')
                                    ->schema([
                                        Forms\Components\Card::make()
                                            ->schema([
                                                Forms\Components\Radio::make('pricing_calculation_method')
                                                    ->label('Fiyatlandırma Yöntemi')
                                                    ->helperText('Bu oda için fiyatlandırma hesaplama yöntemi. Channel entegrasyonlarını etkiler')
                                                    ->options([
                                                        'per_person' => 'Kişi Başı Fiyatlandırma - Fiyat kişi sayısına göre hesaplanır',
                                                        'per_room' => 'Oda Fiyatlandırma (Unit) - Fiyat oda başına sabit olarak hesaplanır',
                                                    ])
                                                    ->default('per_room')
                                                    ->required()
                                                    ->inline(),
                                            ])
                                            ->columns(1)
                                            ->extraAttributes(['class' => 'border-l-4 border-l-success-500 mb-6']),

                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Çocuk Politikaları')
                            ->icon('heroicon-o-face-smile')
                            ->badge(fn () => 'Fiyatlandırma')
                            ->schema([
                                Forms\Components\Section::make('Çocuk Yaş Politikaları')
                                    ->description('Çocuklar için yaşa göre fiyatlandırma kurallarını belirleyin')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        Forms\Components\Repeater::make('child_policies')
                                            ->label('Çocuk Yaş Grupları')
                                            ->helperText('Her yaş grubu için fiyatlandırma politikasını belirleyin')
                                            ->default([])
                                            ->schema([
                                                Forms\Components\Grid::make()
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
                                                    ->columns(2)
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('policy_type')
                                                            ->label('Politika Tipi')
                                                            ->helperText('Bu yaş grubu için fiyat politikası')
                                                            ->options([
                                                                'free' => '🎁 Ücretsiz',
                                                                'discount' => '🏷️ İndirimli',
                                                                'full_price' => '💰 Tam Fiyat',
                                                            ])
                                                            ->required(),
                                                            
                                                        Forms\Components\TextInput::make('discount_percentage')
                                                            ->label('İndirim Yüzdesi')
                                                            ->placeholder('Örn: 50')
                                                            ->helperText('İndirim oranını % olarak belirtin')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(100)
                                                            ->suffix('%')
                                                            ->visible(fn (callable $get) => $get('policy_type') === 'discount')
                                                            ->suffixIcon('heroicon-o-tag'),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpan(1),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => ($state['min_age'] ?? '?') . '-' . ($state['max_age'] ?? '?') . ' yaş: ' . match($state['policy_type'] ?? null) {
                                                'free' => 'Ücretsiz',
                                                'discount' => sprintf('%%%s indirimli', $state['discount_percentage'] ?? '0'),
                                                'full_price' => 'Tam fiyat',
                                                default => 'Belirtilmemiş'
                                            })
                                            ->columns(2)
                                            ->collapsible()
                                            ->defaultItems(1)
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('İçerik')
                            ->icon('heroicon-o-document-text')
                            ->badge(fn () => 'Açıklama')
                            ->schema([
                                Forms\Components\Section::make('Oda Açıklaması')
                                    ->description('Oda hakkında detaylı açıklama ve tanıtım bilgileri')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Detaylı Açıklama')
                                            ->placeholder('Oda özelliklerini ve detaylarını buraya yazın...')
                                            ->helperText('Odanın tüm detaylarını içeren kapsamlı açıklama')
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'h2',
                                                'h3',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'strike',
                                                'undo',
                                            ])
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\KeyValue::make('features_details')
                                            ->label('Özellik Detayları')
                                            ->helperText('Oda özellikleri hakkında kısa açıklamalar (opsiyonel)')
                                            ->addActionLabel('Yeni Özellik Ekle')
                                            ->keyLabel('Özellik')
                                            ->default([])
                                            ->keyPlaceholder('Örn: Klima')
                                            ->valueLabel('Açıklama')
                                            ->valuePlaceholder('Örn: Uzaktan kumandalı, 18000 BTU')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Oda Özellikleri')
                            ->icon('heroicon-o-star')
                            ->badge(fn () => 'Olanaklar')
                            ->schema([
                                Forms\Components\Section::make('Oda Özellikleri ve Olanakları')
                                    ->description('Odada bulunan tüm özellik ve olanakları seçin')
                                    ->icon('heroicon-o-sparkles')
                                    ->schema([
                                        Forms\Components\Card::make()
                                            ->schema([
                                                Forms\Components\Select::make('amenities')
                                                    ->label('Oda Özellikleri')
                                                    ->placeholder('Özellikler seçin veya yenilerini ekleyin')
                                                    ->helperText('Odada mevcut olan tüm özellikleri seçiniz')
                                                    ->relationship('amenities', 'name')
                                                    ->multiple()
                                                    ->preload()
                                                    ->searchable()
                                                    ->createOptionForm([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Özellik Adı')
                                                            ->placeholder('Örn: Jakuzi')
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('Özellik URL')
                                                            ->placeholder('Örn: jakuzi')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('room_amenities', 'slug'),
                                                        Forms\Components\Select::make('category')
                                                            ->label('Kategori')
                                                            ->options([
                                                                'general' => '🏠 Genel',
                                                                'bathroom' => '🚿 Banyo',
                                                                'entertainment' => '🎮 Eğlence',
                                                                'services' => '🛎️ Hizmetler',
                                                                'kitchen' => '🍳 Mini Mutfak',
                                                                'comfort' => '☁️ Konfor',
                                                                'accessibility' => '♿ Erişilebilirlik',
                                                                'view' => '🏞️ Manzara',
                                                            ])
                                                            ->required()
                                                            ->default('general'),
                                                    ]),
                                            ])
                                            ->extraAttributes(['class' => 'border-l-4 border-l-success-500']),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Medya')
                            ->icon('heroicon-o-photo')
                            ->badge(fn () => 'Görseller')
                            ->schema([
                                Forms\Components\Section::make('Oda Görselleri')
                                    ->description('Odaya ait tüm görselleri buradan yükleyebilirsiniz')
                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('cover_image')
                                                            ->label('Kapak Resmi')
                                                            ->helperText('Odayı temsil eden ana görsel (16:9 oranında)')
                                                            ->image()
                                                            ->imageEditor()
                                                            ->imageResizeMode('cover')
                                                            ->imageCropAspectRatio('16:9')
                                                            ->directory('rooms/covers')
                                                            ->columnSpan(1),
                                                    ])
                                                    ->heading('Kapak Resmi')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-primary-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Placeholder::make('preview')
                                                            ->label('Kapak Görsel Önizleme')
                                                            ->content(fn ($record) => $record && $record->cover_image 
                                                                ? view('filament.components.image-preview', ['src' => $record->cover_image_url])
                                                                : 'Kapak görseli yüklendiğinde burada görünecektir.'),
                                                    ])
                                                    ->heading('Önizleme')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-info-500'])
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                            
                                        Forms\Components\Card::make()
                                            ->schema([
                                                Forms\Components\FileUpload::make('gallery')
                                                    ->label('Galeri Görselleri')
                                                    ->helperText('Odayı gösteren detaylı görseller (en fazla 10 adet)')
                                                    ->multiple()
                                                    ->reorderable()
                                                    ->image()
                                                    ->imageEditor()
                                                    ->directory('rooms/gallery')
                                                    ->default([])
                                                    ->maxFiles(10),
                                            ])
                                            ->heading('Oda Galerisi')
                                            ->extraAttributes(['class' => 'border-t-4 border-t-warning-500 mt-4'])
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\Placeholder::make('gallery_preview')
                                            ->label('Galeri Önizleme')
                                            ->content(fn ($record) => $record && !empty($record->gallery_urls) 
                                                ? view('filament.components.image-gallery-preview', ['images' => $record->gallery_urls])
                                                : 'Galeri görselleri yüklendiğinde burada görünecektir.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Durum')
                            ->icon('heroicon-o-check-circle')
                            ->badge(fn () => 'Yayın')
                            ->schema([
                                Forms\Components\Section::make('Yayın Durumu')
                                    ->description('Odanın yayınlanma ve görünürlük ayarları')
                                    ->icon('heroicon-o-check-circle')
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
                                                        Forms\Components\Toggle::make('is_available')
                                                            ->label('Müsait')
                                                            ->helperText('Rezervasyona açık olma durumu')
                                                            ->default(true)
                                                            ->onIcon('heroicon-s-check')
                                                            ->offIcon('heroicon-s-x-mark'),
                                                    ])
                                                    ->heading('Müsaitlik Durumu')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-primary-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Toggle::make('is_featured')
                                                            ->label('Öne Çıkan')
                                                            ->helperText('Öne çıkan odalar listelerde öncelikli gösterilir')
                                                            ->default(false)
                                                            ->onIcon('heroicon-s-star')
                                                            ->offIcon('heroicon-s-x-mark'),
                                                    ])
                                                    ->heading('Öne Çıkarma')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-warning-500'])
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
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
                Tables\Columns\TextColumn::make('floor')
                    ->label('Kat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity_adults')
                    ->label('Yetişkin')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('👤', $state) : '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacity_children')
                    ->label('Çocuk')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('👶', $state) : '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pricing_calculation_method')
                    ->label('Fiyatlandırma')
                    ->badge()
                    ->formatStateUsing(fn (string $state, $record): string => $record->pricing_calculation_method_label)
                    ->color(fn (string $state): string => match ($state) {
                        'per_person' => 'success',
                        'per_room' => 'warning',
                        default => 'gray',
                    }),
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
                Tables\Filters\SelectFilter::make('pricing_calculation_method')
                    ->label('Fiyatlandırma Yöntemi')
                    ->options([
                        'per_person' => 'Kişi Başı Fiyatlandırma',
                        'per_room' => 'Oda Fiyatlandırma (Unit)',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            // BoardTypesRelationManager kaldırıldı - PricingV2 ile yeni mimari kullanılacak
            RelationManagers\ReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}