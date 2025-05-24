<?php

namespace App\Plugins\Accommodation\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\HotelResource\Pages;
use App\Plugins\Accommodation\Models\Hotel;
use App\Plugins\Accommodation\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Schema;
//use App\Plugins\Core\src\Filament\Components\ImageGalleryUpload;
use App\Plugins\Core\src\Models\BaseModel;
use App\Plugins\Core\src\Traits\HasFilamentPermissions;

class HotelResource extends Resource
{
    use HasFilamentPermissions;
    
    protected static ?string $modelLabel = 'Otel';
    protected static ?string $pluralModelLabel = 'Oteller';
    
    // Permission tanımlamaları
    protected static ?string $viewAnyPermission = 'view_hotels';
    protected static ?string $viewPermission = 'view_hotels';
    protected static ?string $createPermission = 'create_hotels';
    protected static ?string $updatePermission = 'update_hotels';
    protected static ?string $deletePermission = 'delete_hotels';
    
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Otel Yönetimi';

    /**
     * Vendor kullanıcıları için navigasyon etiketlerini özelleştirelim
     */
    public static function getNavigationLabel(): string
    {
        // Vendor için daha açıklayıcı bir etiket
        if (auth()->check() && auth()->user()->hasRole('vendor')) {
            return 'Otellerim';
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
            return 10;
        }

        // Varsayılan sıralama
        return static::$navigationSort;
    }

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Otel Bilgileri')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'vertical-tabs'])
                    ->tabs([
                        // Ana Bilgiler Sekmesi - Daha görsel olarak zenginleştirilmiş
                        Forms\Components\Tabs\Tab::make('Genel Bilgiler')
                            ->icon('heroicon-o-building-office')
                            ->badge(fn () => 'Temel')
                            ->schema([
                                // Kartlar içinde daha düzenli gruplar
                                Forms\Components\Section::make('Temel Otel Bilgileri')
                                    ->description('Otelin temel bilgilerini buradan girebilirsiniz')
                                    ->aside()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Otel Adı')
                                                    ->required()
                                                    ->placeholder('Örn: Grand Hotel')
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (string $state, callable $set) {
                                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                                    })
                                                    ->columnSpan(2),

                                                Forms\Components\TextInput::make('slug')
                                                    ->label('URL')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true)
                                                    ->placeholder('Örn: grand-hotel')
                                                    ->helperText('Otelin web adresinde görünecek kısa ismi (otomatik oluşturulur)')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Select::make('star_rating')
                                                    ->label('Yıldız')
                                                    ->options([
                                                        1 => '⭐ 1 Yıldız',
                                                        2 => '⭐⭐ 2 Yıldız',
                                                        3 => '⭐⭐⭐ 3 Yıldız',
                                                        4 => '⭐⭐⭐⭐ 4 Yıldız',
                                                        5 => '⭐⭐⭐⭐⭐ 5 Yıldız',
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Select::make('region_id')
                                                    ->label('Bölge')
                                                    ->relationship(
                                                        'region', 
                                                        'name',
                                                        function ($query) {
                                                            $select = ['id', 'name'];
                                                            if (Schema::hasColumn('regions', 'parent_id')) {
                                                                $select[] = 'parent_id';
                                                            }
                                                            if (Schema::hasColumn('regions', 'type')) {
                                                                $select[] = 'type';
                                                            }
                                                            return $query->select($select);
                                                        }
                                                    )
                                                    ->getOptionLabelFromRecordUsing(function($record) {
                                                        if (Schema::hasColumn('regions', 'parent_id') && method_exists($record, 'getFullPathAttribute')) {
                                                            return $record->full_path;
                                                        }
                                                        return $record->name;
                                                    })
                                                    ->searchable(['name'])
                                                    ->preload()
                                                    ->columnSpan(1)
                                                    ->createOptionForm([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Bölge Adı')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('Bölge URL')
                                                            ->required(),
                                                        ...(Schema::hasColumn('regions', 'parent_id') ? [
                                                            Forms\Components\Select::make('parent_id')
                                                                ->label('Üst Bölge')
                                                                ->relationship('parent', 'name')
                                                                ->searchable()
                                                                ->preload(),
                                                        ] : []),
                                                        ...(Schema::hasColumn('regions', 'type') ? [
                                                            Forms\Components\Select::make('type')
                                                                ->label('Bölge Tipi')
                                                                ->options(Region::getTypeLabels())
                                                                ->default(Region::TYPE_CITY)
                                                                ->required(),
                                                        ] : []),
                                                    ]),
                                                
                                                Forms\Components\Select::make('hotel_type_id')
                                                    ->label('Otel Tipi')
                                                    ->relationship('type', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->createOptionForm([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Ad')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('hotel_types', 'name'),
                                                        Forms\Components\TextInput::make('slug')
                                                            ->label('Slug')
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('hotel_types', 'slug'),
                                                    ])
                                                    ->required()
                                                    ->columnSpan(1),

                                                Forms\Components\Select::make('currency')
                                                    ->label('Para Birimi')
                                                    ->options([
                                                        'TRY' => '₺ TL',
                                                        'USD' => '$ USD',
                                                        'EUR' => '€ EUR',
                                                        'GBP' => '£ GBP',
                                                    ])
                                                    ->required()
                                                    ->default('TRY')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(3),
                                    ]),
                                    
                                Forms\Components\Section::make('Açıklama ve İçerik')
                                    ->description('Otel tanıtım bilgilerini buradan girebilirsiniz')
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\RichEditor::make('short_description')
                                            ->label('Kısa Açıklama')
                                            ->placeholder('Otelin kısa tanıtımını yazınız...')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->columnSpanFull(),
                                            
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Detaylı Açıklama')
                                            ->placeholder('Otelin detaylı tanıtımını yazınız...')
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'codeBlock',
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

                                        Forms\Components\RichEditor::make('refund_policy')
                                            ->label('İade Politikası')
                                            ->placeholder('Otelin iade politikasını yazınız...')
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'h3',
                                                'italic',
                                                'link',
                                                'orderedList',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        // Konum ve İletişim Bilgileri Sekmesi
                        Forms\Components\Tabs\Tab::make('Konum ve İletişim')
                            ->icon('heroicon-o-map')
                            ->schema([
                                // İletişim Bilgileri
                                Forms\Components\Section::make('İletişim Bilgileri')
                                    ->description('Müşterilerin otele ulaşabilecekleri iletişim bilgileri')
                                    ->icon('heroicon-o-phone')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label('E-posta')
                                            ->email()
                                            ->suffixIcon('heroicon-m-envelope')
                                            ->placeholder('ornek@hotel.com'),
                                            
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Telefon')
                                            ->tel()
                                            ->suffixIcon('heroicon-m-phone')
                                            ->placeholder('+90 212 XXX XX XX'),
                                            
                                        Forms\Components\TextInput::make('website')
                                            ->label('Web Sitesi')
                                            ->url()
                                            ->prefixIcon('heroicon-m-globe-alt')
                                            ->placeholder('https://www.ornekhotel.com'),
                                    ])
                                    ->columns(3),
                                
                                // Adres Bilgileri
                                Forms\Components\Section::make('Adres Bilgileri')
                                    ->description('Otelin fiziksel konumu')
                                    ->icon('heroicon-o-map-pin')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Textarea::make('address')
                                                    ->label('Açık Adres')
                                                    ->placeholder('Örn: Example Caddesi No:123')
                                                    ->rows(2)
                                                    ->columnSpan(2),
                                                    
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('city')
                                                            ->label('Şehir')
                                                            ->placeholder('Örn: İstanbul'),
                                                            
                                                        Forms\Components\TextInput::make('state')
                                                            ->label('İlçe/Bölge')
                                                            ->placeholder('Örn: Beşiktaş'),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('zip_code')
                                                    ->label('Posta Kodu')
                                                    ->placeholder('Örn: 34000')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\TextInput::make('country')
                                                    ->label('Ülke')
                                                    ->default('Türkiye')
                                                    ->placeholder('Örn: Türkiye')
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('latitude')
                                                            ->label('Enlem')
                                                            ->placeholder('Örn: 41.0082')
                                                            ->suffixIcon('heroicon-m-map')
                                                            ->numeric()
                                                            ->step('0.000001'),
                                                            
                                                        Forms\Components\TextInput::make('longitude')
                                                            ->label('Boylam')
                                                            ->placeholder('Örn: 28.9784')
                                                            ->suffixIcon('heroicon-m-map')
                                                            ->numeric()
                                                            ->step('0.000001'),
                                                    ])
                                                    ->columns(2)
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(3),
                                    ]),
                            ]),
                            
                        // Görseller Sekmesi
                        Forms\Components\Tabs\Tab::make('Görseller')
                            ->icon('heroicon-o-photo')
                            ->badge(fn () => 'Medya')
                            ->schema([
                                Forms\Components\Section::make('Otel Görselleri')
                                    ->description('Otelin görsellerini yükleyiniz')
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\FileUpload::make('cover_image')
                                                    ->label('Kapak Resmi')
                                                    ->image()
                                                    ->disk('public')
                                                    ->imageEditor()
                                                    ->imageResizeMode('cover')
                                                    ->imageCropAspectRatio('16:9')
                                                    ->imageResizeTargetWidth('1200')
                                                    ->imageResizeTargetHeight('675')
                                                    ->directory('hotels/covers')
                                                    ->visibility('public')
                                                    ->columnSpan(1)
                                                    ->helperText('Ana sayfada görüntülenecek görseli yükleyiniz (16:9 oranında)'),
                                                
                                                Forms\Components\Placeholder::make('preview')
                                                    ->label('Kapak Görsel Önizleme')
                                                    ->content(fn ($record) => $record && $record->cover_image 
                                                        ? view('filament.components.image-preview', ['src' => $record->cover_image_url])
                                                        : view('filament.components.image-preview', ['src' => null]))
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                          
                                        // Standart FileUpload bileşenini kullanalım
                                        Forms\Components\FileUpload::make('gallery')
                                            ->label('Galeri Görselleri')
                                            ->multiple()
                                            ->reorderable()
                                            ->image()
                                            ->disk('public')
                                            ->imageEditor()
                                            ->imageResizeMode('cover')
                                            ->directory('hotels/gallery')
                                            ->visibility('public')
                                            ->columnSpanFull()
                                            ->maxFiles(10)
                                            ->helperText('En fazla 10 adet görsel yükleyebilirsiniz'),
                                            
                                        Forms\Components\Placeholder::make('gallery_preview')
                                            ->label('Galeri Önizleme')
                                            ->content(fn ($record) => $record && !empty($record->gallery_urls) 
                                                ? view('filament.components.image-gallery-preview', ['images' => $record->gallery_urls])
                                                : view('filament.components.image-gallery-preview', ['images' => []]))
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                            
                        // Kurallar ve Saatler Sekmesi 
                        Forms\Components\Tabs\Tab::make('Kurallar ve Çalışma Saatleri')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                // Giriş/Çıkış Saatleri
                                Forms\Components\Section::make('Giriş/Çıkış Saatleri')
                                    ->description('Müşterilerin giriş-çıkış yapabileceği saatleri belirleyin')
                                    ->icon('heroicon-o-clock')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TimePicker::make('check_in_from')
                                                            ->label('En Erken Giriş Saati')
                                                            ->seconds(false)
                                                            ->default('14:00')
                                                            ->formatStateUsing(function ($state, $record) {
                                                                if ($record) {
                                                                    $checkInOut = $record->check_in_out;
                                                                    return $checkInOut['check_in_from'] ?? '14:00';
                                                                }
                                                                return '14:00';
                                                            })
                                                            ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                                                $checkInOut = [
                                                                    'check_in_from' => $state,
                                                                    'check_in_until' => $record->check_in_out['check_in_until'] ?? '23:59',
                                                                    'check_out_from' => $record->check_in_out['check_out_from'] ?? '07:00',
                                                                    'check_out_until' => $record->check_in_out['check_out_until'] ?? '12:00',
                                                                ];
                                                                $set('check_in_out', $checkInOut);
                                                            }),
                                                            
                                                        Forms\Components\TimePicker::make('check_in_until')
                                                            ->label('En Geç Giriş Saati')
                                                            ->seconds(false)
                                                            ->default('23:59')
                                                            ->formatStateUsing(function ($state, $record) {
                                                                if ($record) {
                                                                    $checkInOut = $record->check_in_out;
                                                                    return $checkInOut['check_in_until'] ?? '23:59';
                                                                }
                                                                return '23:59';
                                                            })
                                                            ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                                                $checkInOut = [
                                                                    'check_in_from' => $record->check_in_out['check_in_from'] ?? '14:00',
                                                                    'check_in_until' => $state,
                                                                    'check_out_from' => $record->check_in_out['check_out_from'] ?? '07:00',
                                                                    'check_out_until' => $record->check_in_out['check_out_until'] ?? '12:00',
                                                                ];
                                                                $set('check_in_out', $checkInOut);
                                                            }),
                                                    ])
                                                    ->columns(2)
                                                    ->heading('Giriş (Check-in) Saatleri')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-primary-500'])
                                                    ->columnSpan(1),
                                                    
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TimePicker::make('check_out_from')
                                                            ->label('En Erken Çıkış Saati')
                                                            ->seconds(false)
                                                            ->default('07:00')
                                                            ->formatStateUsing(function ($state, $record) {
                                                                if ($record) {
                                                                    $checkInOut = $record->check_in_out;
                                                                    return $checkInOut['check_out_from'] ?? '07:00';
                                                                }
                                                                return '07:00';
                                                            })
                                                            ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                                                $checkInOut = [
                                                                    'check_in_from' => $record->check_in_out['check_in_from'] ?? '14:00',
                                                                    'check_in_until' => $record->check_in_out['check_in_until'] ?? '23:59',
                                                                    'check_out_from' => $state,
                                                                    'check_out_until' => $record->check_in_out['check_out_until'] ?? '12:00',
                                                                ];
                                                                $set('check_in_out', $checkInOut);
                                                            }),
                                                            
                                                        Forms\Components\TimePicker::make('check_out_until')
                                                            ->label('En Geç Çıkış Saati')
                                                            ->seconds(false)
                                                            ->default('12:00')
                                                            ->formatStateUsing(function ($state, $record) {
                                                                if ($record) {
                                                                    $checkInOut = $record->check_in_out;
                                                                    return $checkInOut['check_out_until'] ?? '12:00';
                                                                }
                                                                return '12:00';
                                                            })
                                                            ->afterStateUpdated(function ($state, Forms\Set $set, $record) {
                                                                $checkInOut = [
                                                                    'check_in_from' => $record->check_in_out['check_in_from'] ?? '14:00',
                                                                    'check_in_until' => $record->check_in_out['check_in_until'] ?? '23:59',
                                                                    'check_out_from' => $record->check_in_out['check_out_from'] ?? '07:00',
                                                                    'check_out_until' => $state,
                                                                ];
                                                                $set('check_in_out', $checkInOut);
                                                            }),
                                                    ])
                                                    ->columns(2)
                                                    ->heading('Çıkış (Check-out) Saatleri')
                                                    ->extraAttributes(['class' => 'border-l-4 border-l-warning-500'])
                                                    ->columnSpan(1),
                                                
                                                Forms\Components\Hidden::make('check_in_out')
                                                    ->default([
                                                        'check_in_from' => '14:00',
                                                        'check_in_until' => '23:59',
                                                        'check_out_from' => '07:00',
                                                        'check_out_until' => '12:00',
                                                    ]),
                                            ])
                                            ->columns(2),
                                    ]),
                                
                                // İade Politikaları
                                Forms\Components\Section::make('İade Seçenekleri')
                                    ->description('İade edilebilir ve iade edilemez rezervasyon seçenekleri')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Toggle::make('allow_refundable')
                                                            ->label('İade Edilebilir Rezervasyonlara İzin Ver')
                                                            ->helperText('İade edilebilir rezervasyonlar tamamen veya kısmen iade edilebilir')
                                                            ->default(true)
                                                            ->onIcon('heroicon-s-check')
                                                            ->offIcon('heroicon-s-x-mark'),
                                                    ])
                                                    ->columnSpan(1),

                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Toggle::make('allow_non_refundable')
                                                            ->label('İade Edilemez Rezervasyonlara İzin Ver')
                                                            ->helperText('İade edilemez rezervasyonlar genellikle daha uygun fiyatlıdır')
                                                            ->default(true)
                                                            ->onIcon('heroicon-s-check')
                                                            ->offIcon('heroicon-s-x-mark'),
                                                    ])
                                                    ->columnSpan(1),

                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('non_refundable_discount')
                                                            ->label('İade Edilemez İndirim Oranı (%)')
                                                            ->helperText('İade edilemez rezervasyonlara uygulanacak indirim yüzdesi')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->maxValue(100)
                                                            ->suffix('%'),
                                                    ])
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(3),
                                    ]),

                                // Otel Politikaları
                                Forms\Components\Section::make('Otel Politikaları')
                                    ->description('Otel kuralları ve politikaları')
                                    ->icon('heroicon-o-document-check')
                                    ->collapsible()
                                    ->schema([
                                        Forms\Components\Repeater::make('policies')
                                            ->label('Politikalar')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Başlık')
                                                    ->required()
                                                    ->placeholder('Örn: Evcil Hayvan Politikası'),
                                                    
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Açıklama')
                                                    ->required()
                                                    ->placeholder('Örn: Otelimizde küçük evcil hayvanlar ilave ücret karşılığında kabul edilmektedir.'),
                                            ])
                                            ->columns(2)
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->collapsible()
                                            ->addActionLabel('Yeni Politika Ekle')
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),
                            
                        // Özellikler Sekmesi
                        Forms\Components\Tabs\Tab::make('Otel Özellikleri')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('tags')
                                                            ->label('Etiketler')
                                                            ->relationship('tags', 'name')
                                                            ->multiple()
                                                            ->searchable()
                                                            ->preload()
                                                            ->createOptionForm([
                                                                Forms\Components\TextInput::make('name')
                                                                    ->label('Etiket Adı')
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('slug')
                                                                    ->label('Etiket URL')
                                                                    ->required(),
                                                            ])
                                                            ->placeholder('Otele uygun etiketleri seçin')
                                                            ->helperText('Otele uygun kategorileri seçiniz'),
                                                    ])
                                                    ->heading('Otel Etiketleri')
                                                    ->columnSpan(1),

                                                Forms\Components\Card::make()
                                                    ->schema([
                                                        Forms\Components\Select::make('amenities')
                                                            ->label('Otel Özellikleri')
                                                            ->relationship('amenities', 'name')
                                                            ->multiple()
                                                            ->searchable()
                                                            ->preload()
                                                            ->createOptionForm([
                                                                Forms\Components\TextInput::make('name')
                                                                    ->label('Ad')
                                                                    ->required()
                                                                    ->maxLength(255),
                                                                Forms\Components\TextInput::make('slug')
                                                                    ->label('Slug')
                                                                    ->required()
                                                                    ->maxLength(255)
                                                                    ->unique('hotel_amenities', 'slug'),
                                                                Forms\Components\Select::make('category')
                                                                    ->label('Kategori')
                                                                    ->options([
                                                                        'general' => 'Genel',
                                                                        'room' => 'Oda',
                                                                        'bathroom' => 'Banyo',
                                                                        'kitchen' => 'Mutfak',
                                                                        'entertainment' => 'Eğlence',
                                                                        'services' => 'Hizmetler',
                                                                    ])
                                                                    ->required()
                                                                    ->default('general'),
                                                            ])
                                                            ->placeholder('Otelde bulunan özellikleri seçin')
                                                            ->helperText('Otelde bulunan özellikleri ve hizmetleri seçiniz'),
                                                    ])
                                                    ->heading('Otel Olanakları')
                                                    ->columnSpan(1),
                                            ])
                                            ->columns(2),
                                    ]),
                            ]),

                        // Çocuk Politikası Sekmesi
                        Forms\Components\Tabs\Tab::make('Çocuk Politikası')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\Section::make('Genel Çocuk Politikası')
                                    ->description('Otel genelinde geçerli olacak çocuk politikası ayarları. Odalar bu politikayı miras alır.')
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('max_children_per_room')
                                                    ->label('Oda Başına Maksimum Çocuk Sayısı')
                                                    ->numeric()
                                                    ->default(2)
                                                    ->minValue(0)
                                                    ->maxValue(5)
                                                    ->required()
                                                    ->helperText('Her odada konaklamasına izin verilen maksimum çocuk sayısı'),
                                                    
                                                Forms\Components\TextInput::make('child_age_limit')
                                                    ->label('Çocuk Yaş Sınırı')
                                                    ->numeric()
                                                    ->default(12)
                                                    ->minValue(0)
                                                    ->maxValue(18)
                                                    ->required()
                                                    ->helperText('Kaç yaşına kadar çocuk sayılacağı'),
                                                    
                                                Forms\Components\Toggle::make('children_stay_free')
                                                    ->label('Çocuklar Ücretsiz Konaklasın')
                                                    ->default(false)
                                                    ->helperText('İşaretlenirse çocuklar ücretsiz konaklayabilir'),
                                            ])
                                            ->columns(3),
                                            
                                        Forms\Components\Textarea::make('child_policy_description')
                                            ->label('Çocuk Politikası Açıklaması')
                                            ->rows(3)
                                            ->helperText('Müşterilere gösterilecek detaylı açıklama')
                                            ->placeholder('Örn: 0-6 yaş arası çocuklar ücretsiz, 7-12 yaş arası çocuklar %50 indirimli konaklayabilir.'),
                                    ]),
                                    
                                Forms\Components\Section::make('Yaş Aralığına Göre Fiyatlandırma')
                                    ->description('Farklı yaş grupları için özel fiyatlandırma kuralları')
                                    ->schema([
                                        Forms\Components\Repeater::make('child_policies')
                                            ->label('Çocuk Yaş Grupları ve Fiyatlandırma')
                                            ->schema([
                                                Forms\Components\Grid::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('age_from')
                                                            ->label('Başlangıç Yaşı')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->required(),
                                                            
                                                        Forms\Components\TextInput::make('age_to')
                                                            ->label('Bitiş Yaşı')
                                                            ->numeric()
                                                            ->default(6)
                                                            ->minValue(0)
                                                            ->required(),
                                                            
                                                        Forms\Components\Select::make('price_type')
                                                            ->label('Fiyat Tipi')
                                                            ->options([
                                                                'free' => 'Ücretsiz',
                                                                'percentage' => 'Yüzde İndirim',
                                                                'fixed' => 'Sabit Ücret',
                                                            ])
                                                            ->default('free')
                                                            ->required()
                                                            ->reactive(),
                                                            
                                                        Forms\Components\TextInput::make('price_value')
                                                            ->label(fn (Forms\Get $get) => match($get('price_type')) {
                                                                'percentage' => 'İndirim Yüzdesi',
                                                                'fixed' => 'Sabit Ücret',
                                                                default => 'Değer'
                                                            })
                                                            ->numeric()
                                                            ->default(0)
                                                            ->minValue(0)
                                                            ->hidden(fn (Forms\Get $get): bool => $get('price_type') === 'free')
                                                            ->suffix(fn (Forms\Get $get) => $get('price_type') === 'percentage' ? '%' : '₺'),
                                                            
                                                        Forms\Components\TextInput::make('max_children')
                                                            ->label('Max Çocuk')
                                                            ->numeric()
                                                            ->default(2)
                                                            ->minValue(1)
                                                            ->helperText('Bu yaş grubundan max kaç çocuk'),
                                                            
                                                        Forms\Components\TextInput::make('description')
                                                            ->label('Açıklama')
                                                            ->placeholder('Örn: 0-6 yaş ücretsiz')
                                                            ->columnSpan(2),
                                                    ])
                                                    ->columns(7),
                                            ])
                                            ->defaultItems(2)
                                            ->reorderable()
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn (array $state): ?string => isset($state['age_from'], $state['age_to']) 
                                                ? "{$state['age_from']}-{$state['age_to']} yaş" 
                                                : null),
                                    ]),
                                    
                                Forms\Components\Section::make('Bilgilendirme')
                                    ->description('Önemli notlar')
                                    ->schema([
                                        Forms\Components\Placeholder::make('info')
                                            ->content('Bu politika otel genelinde geçerli olacaktır. Odalar varsayılan olarak bu politikayı kullanır, ancak her oda kendi özel politikasını tanımlayabilir.')
                                            ->helperText('Oda düzeyinde yapılan değişiklikler, otel politikasını ezmez, sadece o oda için geçerli olur.'),
                                    ]),
                            ]),
                            

                        // Board Types Sekmesi
                        Forms\Components\Tabs\Tab::make('Pansiyon Tipleri')
                            ->icon('heroicon-o-cake')
                            ->schema([
                                Forms\Components\Section::make('Otel Pansiyon Tipleri')
                                    ->description('Bu otelde geçerli olan pansiyon tipleri')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('boardTypes')
                                            ->label('Pansiyon Tipleri')
                                            ->relationship(
                                                'boardTypes',
                                                'name',
                                                fn ($query) => $query->where('is_active', true)
                                            )
                                            ->columns(2)
                                            ->helperText('Bu otelde sunulan pansiyon tiplerini seçin. Otelde geçerli olan tüm pansiyon seçeneklerini işaretleyin.')
                                            ->required(),
                                    ]),
                            ]),

                        // Vendor Sekmesi (Sadece adminler için)
                        Forms\Components\Tabs\Tab::make('Vendor Bilgileri')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Forms\Components\Section::make('Vendor (Partner) Bilgileri')
                                    ->description('Bu otelin bağlı olduğu vendor/partner bilgileri')
                                    ->schema([
                                        Forms\Components\Select::make('vendor_id')
                                            ->label('Vendor (Partner)')
                                            ->relationship('vendor', 'company_name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Bu otelin hangi vendora/partnere ait olduğunu seçin.')
                                            ->placeholder('Vendor seçiniz'),
                                    ]),
                            ])
                            ->visible(fn () => auth()->user() && auth()->user()->hasRole('admin|super_admin')),

                        // SEO Sekmesi
                        Forms\Components\Tabs\Tab::make('SEO ve Durum')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\Section::make('SEO Bilgileri')
                                            ->description('Arama motorları için SEO bilgileri')
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->collapsible()
                                            ->schema([
                                                Forms\Components\TextInput::make('meta_title')
                                                    ->label('Meta Başlık')
                                                    ->placeholder('Örn: Lüks Otel - 5 Yıldızlı Konaklama')
                                                    ->helperText('Tarayıcı sekmesinde ve arama sonuçlarında görünecek başlık'),
                                                    
                                                Forms\Components\Textarea::make('meta_description')
                                                    ->label('Meta Açıklama')
                                                    ->placeholder('Örn: Muhteşem manzara eşliğinde 5 yıldızlı lüks konaklama deneyimi.')
                                                    ->helperText('Arama sonuçlarında görünecek kısa açıklama'),
                                                    
                                                Forms\Components\Textarea::make('meta_keywords')
                                                    ->label('Meta Anahtar Kelimeler')
                                                    ->placeholder('Örn: lüks otel, 5 yıldızlı, spa, havuz')
                                                    ->helperText('Arama motorları için anahtar kelimeler (virgülle ayırın)'),
                                            ])
                                            ->columnSpan(['lg' => 2]),
                                            
                                        Forms\Components\Section::make('Durum')
                                            ->description('Otelin aktiflik durumunu belirleyin')
                                            ->icon('heroicon-o-check-circle')
                                            ->collapsible()
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
                                                            ->extraAttributes(['class' => 'border-l-4 border-l-success-500']),
                                                            
                                                        Forms\Components\Card::make()
                                                            ->schema([
                                                                Forms\Components\Toggle::make('is_featured')
                                                                    ->label('Öne Çıkan')
                                                                    ->helperText('Öne çıkan oteller anasayfada görüntülenebilir')
                                                                    ->default(false)
                                                                    ->onIcon('heroicon-s-star')
                                                                    ->offIcon('heroicon-s-x-mark'),
                                                            ])
                                                            ->extraAttributes(['class' => 'border-l-4 border-l-warning-500']),
                                                    ])
                                                    ->columns(2),
                                            ])
                                            ->columnSpan(['lg' => 1]),
                                    ])
                                    ->columns(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Otel Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('region.name')
                    ->label('Bölge')
                    ->tooltip(function (Hotel $record): string {
                        if (Schema::hasColumn('regions', 'parent_id') && method_exists($record->region, 'getFullPathAttribute')) {
                            return $record->region?->full_path ?? '';
                        }
                        return $record->region?->name ?? '';
                    })
                    ->limit(30)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('region', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                            if (Schema::hasColumn('regions', 'parent_id')) {
                                $q->orWhereHas('parent', function (Builder $q2) use ($search) {
                                    $q2->where('name', 'like', "%{$search}%");
                                });
                            }
                        });
                    }),
                Tables\Columns\TextColumn::make('region.type')
                    ->label('Bölge Tipi')
                    ->formatStateUsing(function ($state, Hotel $record) {
                        // Check if the 'type' column exists
                        if (!Schema::hasColumn('regions', 'type')) {
                            return 'Bölge';
                        }
                        
                        try {
                            return $record->region?->getTypeLabel() ?? 'Belirtilmemiş';
                        } catch (\Throwable $e) {
                            return 'Belirtilmemiş';
                        }
                    })
                    ->badge()
                    ->color(function ($state, Hotel $record) {
                        // Check if the 'type' column exists
                        if (!Schema::hasColumn('regions', 'type')) {
                            return 'gray';
                        }
                        
                        $type = $record->region?->type;
                        
                        if ($type === null) {
                            return 'gray';
                        }
                        
                        return match ($type) {
                            Region::TYPE_COUNTRY => 'success',
                            Region::TYPE_REGION => 'info',
                            Region::TYPE_CITY => 'warning',
                            Region::TYPE_DISTRICT => 'danger',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('type.name')
                    ->label('Otel Tipi')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('star_rating')
                    ->label('Yıldız')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('⭐', $state) : ''),
                Tables\Columns\TextColumn::make('vendor.company_name')
                    ->label('Vendor (Partner)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->visible(fn () => auth()->user() && auth()->user()->hasRole('admin|super_admin')),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Öne Çıkan'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->label('Ülke')
                    ->options(function () {
                        // Check if the 'type' column exists
                        if (Schema::hasColumn('regions', 'type')) {
                            return Region::where('type', Region::TYPE_COUNTRY)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                        return [];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        
                        $countryId = $data['value'];
                        
                        // Ülkeye doğrudan bağlı oteller
                        $directHotels = $query->where('region_id', $countryId);
                        
                        // Ülkeye bağlı alt bölgelerdeki oteller
                        $country = Region::find($countryId);
                        if ($country) {
                            $childRegionIds = $country->getAllChildrenIdsAttribute();
                            if (!empty($childRegionIds)) {
                                $directHotels->orWhereIn('region_id', $childRegionIds);
                            }
                        }
                        
                        return $directHotels;
                    }),

                Tables\Filters\SelectFilter::make('main_region')
                    ->label('Ana Bölge')
                    ->options(function () {
                        // Check if the 'type' column exists
                        if (Schema::hasColumn('regions', 'type')) {
                            return Region::where('type', Region::TYPE_REGION)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                        return [];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        
                        $regionId = $data['value'];
                        
                        // Bölgeye doğrudan bağlı oteller
                        $directHotels = $query->where('region_id', $regionId);
                        
                        // Bölgeye bağlı alt bölgelerdeki oteller
                        $region = Region::find($regionId);
                        if ($region) {
                            $childRegionIds = $region->getAllChildrenIdsAttribute();
                            if (!empty($childRegionIds)) {
                                $directHotels->orWhereIn('region_id', $childRegionIds);
                            }
                        }
                        
                        return $directHotels;
                    }),

                Tables\Filters\SelectFilter::make('city')
                    ->label('Şehir')
                    ->options(function () {
                        // Check if the 'type' column exists
                        if (Schema::hasColumn('regions', 'type')) {
                            return Region::where('type', Region::TYPE_CITY)
                                ->pluck('name', 'id')
                                ->toArray();
                        }
                        return [];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        
                        $cityId = $data['value'];
                        
                        // Şehre doğrudan bağlı oteller
                        $directHotels = $query->where('region_id', $cityId);
                        
                        // Şehre bağlı ilçelerdeki oteller
                        $city = Region::find($cityId);
                        if ($city) {
                            $districtIds = $city->children()->pluck('id')->toArray();
                            if (!empty($districtIds)) {
                                $directHotels->orWhereIn('region_id', $districtIds);
                            }
                        }
                        
                        return $directHotels;
                    }),

                Tables\Filters\SelectFilter::make('hotel_type_id')
                    ->label('Otel Tipi')
                    ->relationship('type', 'name'),
                    
                Tables\Filters\SelectFilter::make('star_rating')
                    ->label('Yıldız')
                    ->options([
                        '1' => '⭐ 1 Yıldız',
                        '2' => '⭐⭐ 2 Yıldız',
                        '3' => '⭐⭐⭐ 3 Yıldız',
                        '4' => '⭐⭐⭐⭐ 4 Yıldız',
                        '5' => '⭐⭐⭐⭐⭐ 5 Yıldız',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Öne Çıkan'),

                Tables\Filters\SelectFilter::make('vendor_id')
                    ->label('Vendor (Partner)')
                    ->relationship('vendor', 'company_name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user() && auth()->user()->hasRole('admin|super_admin')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pricing')
                    ->label('Fiyatlar')
                    ->color('success')
                    ->icon('heroicon-o-currency-dollar')
                    ->url(fn (Hotel $record): string => route('filament.admin.pages.hotel-pricing-page', ['hotel_id' => $record->id]))
                    ->openUrlInNewTab(),
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
            HotelResource\RelationManagers\RoomsRelationManager::class,
            HotelResource\RelationManagers\ContactsRelationManager::class,
            HotelResource\RelationManagers\TagsRelationManager::class,
        ];
    }
    
    public static function getActions(): array
    {
        return [
            \Filament\Actions\Action::make('managePricing')
                ->label('Fiyatlandırma Yönetimi')
                ->url(fn (Hotel $record): string => route('filament.admin.pages.hotel-pricing-management', ['hotel_id' => $record->id]))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            // PricingV2 action kaldırıldı
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}