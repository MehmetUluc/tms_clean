<?php

namespace App\Plugins\Accommodation\Filament\Resources;

use App\Plugins\Accommodation\Filament\Resources\RoomTypeResource\Pages;
use App\Plugins\Accommodation\Filament\Resources\RoomTypeResource\RelationManagers;
use App\Plugins\Accommodation\Models\RoomType;
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
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Plugins\Core\src\Traits\HasFilamentPermissions;

class RoomTypeResource extends Resource
{
    use HasFilamentPermissions;

    protected static ?string $viewAnyPermission = 'view_room_types';
    protected static ?string $viewPermission = 'view_room_types';
    protected static ?string $createPermission = 'create_room_types';
    protected static ?string $updatePermission = 'update_room_types';
    protected static ?string $deletePermission = 'delete_room_types';
    protected static ?string $model = RoomType::class;protected static ?string $modelLabel = 'Oda Tipi';
    protected static ?string $pluralModelLabel = 'Oda Tipleri';
    protected static ?string $navigationLabel = 'Oda Tipleri';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Oda Yönetimi';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Oda Tipi Bilgileri')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'vertical-tabs'])
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Temel Bilgiler')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Ad')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Select::make('icon')
                                    ->label('İkon')
                                    ->options([
                                        // Otel ve oda ile ilgili ikonlar (Heroicons'da mevcut olan)
                                        'home-modern' => 'Modern Ev (Yatak Odası)',
                                        'home' => 'Ev',
                                        'building-office' => 'Bina',
                                        'building-office-2' => 'Büyük Bina',
                                        'building-storefront' => 'Dükkan',
                                        'rectangle-stack' => 'Odalar',
                                        'squares-2x2' => 'Kareler', 
                                        'squares-plus' => 'Oda Ekle',
                                        
                                        // Misafir/kişi ile ilgili ikonlar
                                        'user' => 'Tek Misafir',
                                        'user-group' => 'Misafir Grubu',
                                        'user-plus' => 'Misafir Ekle',
                                        'users' => 'Misafirler',
                                        
                                        // Popüler özellikler
                                        'star' => 'Yıldız',
                                        'sparkles' => 'Parıltı',
                                        'heart' => 'Kalp',
                                        'key' => 'Anahtar',
                                        'map-pin' => 'Konum',
                                        'globe-alt' => 'Dünya',
                                        'globe-europe-africa' => 'Avrupa',
                                        
                                        // Servisler/konfor
                                        'wifi' => 'WiFi',
                                        'tv' => 'TV',
                                        'device-phone-mobile' => 'Telefon',
                                        'computer-desktop' => 'Bilgisayar',
                                        'device-tablet' => 'Tablet',
                                        'cake' => 'Pasta/Kahvaltı',
                                        'truck' => 'Taşıma/Transport',
                                        'shopping-cart' => 'Alışveriş',
                                        
                                        // Diğer
                                        'sun' => 'Güneş',
                                        'moon' => 'Ay/Gece',
                                        'cloud' => 'Bulut',
                                        'academic-cap' => 'Akademik',
                                        'lifebuoy' => 'Can Simidi',
                                        'banknotes' => 'Para',
                                    ])
                                    ->searchable()
                                    ->default('home-modern')
                                    ->required(),
                                Forms\Components\RichEditor::make('description')
                                    ->label('Açıklama')
                                    ->columnSpanFull(),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Kapasite Bilgileri')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\TextInput::make('max_adults')
                                    ->label('Maksimum Yetişkin')
                                    ->required()
                                    ->numeric()
                                    ->default(2)
                                    ->minValue(1),
                                Forms\Components\TextInput::make('max_children')
                                    ->label('Maksimum Çocuk')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0),
                                Forms\Components\TextInput::make('max_guests')
                                    ->label('Toplam Misafir Kapasitesi')
                                    ->required()
                                    ->numeric()
                                    ->default(3)
                                    ->minValue(1),
                                Forms\Components\TextInput::make('size')
                                    ->label('Metrekare')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('m²'),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Yatak Konfigürasyonu')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Repeater::make('bed_configuration')
                                    ->label('Yatak Konfigürasyonu')
                                    ->schema([
                                        Forms\Components\Select::make('bed_type')
                                            ->label('Yatak Tipi')
                                            ->options([
                                                'single' => 'Tek Kişilik Yatak',
                                                'twin' => 'İki Tek Kişilik Yatak',
                                                'double' => 'Çift Kişilik Yatak',
                                                'queen' => 'Queen Size Yatak',
                                                'king' => 'King Size Yatak',
                                                'sofa_bed' => 'Çekyat',
                                                'bunk_bed' => 'Ranza',
                                                'baby_cot' => 'Bebek Karyolası',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('count')
                                            ->label('Adet')
                                            ->numeric()
                                            ->minValue(1)
                                            ->default(1)
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(1),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Manzara & Konum')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Select::make('view_type')
                                    ->label('Manzara Tipi')
                                    ->multiple()
                                    ->options([
                                        'sea' => 'Deniz Manzarası',
                                        'mountain' => 'Dağ Manzarası',
                                        'garden' => 'Bahçe Manzarası',
                                        'pool' => 'Havuz Manzarası',
                                        'city' => 'Şehir Manzarası',
                                        'forest' => 'Orman Manzarası',
                                        'lake' => 'Göl Manzarası',
                                        'no_view' => 'Manzarasız',
                                    ]),
                                Forms\Components\Select::make('location')
                                    ->label('Konum')
                                    ->multiple()
                                    ->options([
                                        'main_building' => 'Ana Bina',
                                        'annex_building' => 'Ek Bina',
                                        'villa' => 'Villa',
                                        'bungalow' => 'Bungalov',
                                        'ground_floor' => 'Zemin Kat',
                                        'high_floor' => 'Üst Kat',
                                        'beach_front' => 'Sahil Kenarı',
                                        'garden' => 'Bahçe İçi',
                                    ]),
                            ]),
                            
                        Forms\Components\Tabs\Tab::make('Özellikler')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Forms\Components\Repeater::make('features')
                                    ->label('Standart Özellikler')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Özellik Adı')
                                            ->required(),
                                        Forms\Components\Select::make('category')
                                            ->label('Kategori')
                                            ->options([
                                                'general' => 'Genel',
                                                'bathroom' => 'Banyo',
                                                'entertainment' => 'Eğlence',
                                                'services' => 'Hizmetler',
                                                'comfort' => 'Konfor',
                                                'kitchen' => 'Mutfak',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('icon')
                                            ->label('İkon')
                                            ->helperText('Heroicon adı veya sembol'),
                                    ])
                                    ->columns(3),
                            ]),
                            
                        // Pansiyon Tipleri artık Room (Oda) modeline taşınmıştır
                        
                        Forms\Components\Tabs\Tab::make('Durum')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sıralama')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Select::make('status')
                                    ->label('Durum')
                                    ->options([
                                        'active' => 'Aktif',
                                        'inactive' => 'Pasif',
                                        'coming_soon' => 'Yakında',
                                        'under_renovation' => 'Tadilatta',
                                    ])
                                    ->default('active'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->label('İkon')
                    ->formatStateUsing(function ($state) {
                        $iconName = $state ?? 'home-modern';
                        // Ön eklerini temizle
                        $iconName = str_replace(['heroicon-o-', 'heroicon-'], '', $iconName);
                        
                        // Geçerli bir ikon olup olmadığını kontrol et
                        $validIcons = [
                            'home-modern', 'home', 'building-office', 'building-office-2', 
                            'building-storefront', 'rectangle-stack', 'squares-2x2', 
                            'squares-plus', 'user', 'user-group', 'user-plus', 'users',
                            'star', 'sparkles', 'heart', 'key', 'map-pin', 'globe-alt',
                            'globe-europe-africa', 'wifi', 'tv', 'device-phone-mobile',
                            'computer-desktop', 'device-tablet', 'cake', 'truck',
                            'shopping-cart', 'sun', 'moon', 'cloud', 'academic-cap',
                            'lifebuoy', 'banknotes'
                        ];
                        
                        if (!in_array($iconName, $validIcons)) {
                            $iconName = 'home-modern';
                        }
                        
                        return '<div class="flex justify-center h-6 w-6">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                ' . self::getHeroIconSvgPath($iconName) . '
                            </svg>
                        </div>';
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_adults')
                    ->label('Yetişkin')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('👤', $state) : '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_children')
                    ->label('Çocuk')
                    ->formatStateUsing(fn ($state): string => $state ? str_repeat('👶', $state) : '')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_guests')
                    ->label('Toplam')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('m²')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_type')
                    ->label('Manzara')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return '-';
                        if (is_string($state)) return $state;
                        if (is_array($state)) {
                            return implode(', ', array_map(function ($item) {
                                return match ($item) {
                                    'sea' => 'Deniz',
                                    'mountain' => 'Dağ',
                                    'garden' => 'Bahçe',
                                    'pool' => 'Havuz',
                                    'city' => 'Şehir',
                                    'forest' => 'Orman',
                                    'lake' => 'Göl',
                                    'no_view' => 'Manzarasız',
                                    default => $item,
                                };
                            }, $state));
                        }
                        return '-';
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıralama')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
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
            RelationManagers\RoomsRelationManager::class,
            RelationManagers\BoardTypesRelationManager::class,
            RelationManagers\RateRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomTypes::route('/'),
            'create' => Pages\CreateRoomType::route('/create'),
            'edit' => Pages\EditRoomType::route('/{record}/edit'),
        ];
    }
    
    /**
     * Belirtilen ikon adı için SVG path değerini döndürür
     */
    public static function getHeroIconSvgPath(string $iconName): string
    {
        $paths = [
            'home-modern' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />',
            'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
            'building-office' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />',
            'building-office-2' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />',
            'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />',
            'user-group' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />',
            'star' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />',
            'default' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />',
        ];
        
        return $paths[$iconName] ?? $paths['default'];
    }
}