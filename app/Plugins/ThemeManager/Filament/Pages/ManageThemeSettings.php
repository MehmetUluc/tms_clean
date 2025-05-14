<?php

namespace App\Plugins\ThemeManager\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\ViewComponent;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Group;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use App\Plugins\ThemeManager\Models\ThemeSetting;
use App\Plugins\ThemeManager\Services\ThemeManagerService;
use App\Plugins\ThemeManager\Services\ColorPaletteService;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Livewire\WithFileUploads;
use Filament\Forms\Components\RichEditor;

class ManageThemeSettings extends Page
{
    use WithFileUploads;
    
    /**
     * Bu metod artık kullanımda değil
     */
    
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static string $view = 'filament.pages.manage-theme-settings';
    
    protected static ?string $navigationLabel = 'Tema Yöneticisi';
    
    protected static ?string $title = 'Tema Ayarları';
    
    protected static ?string $slug = 'theme-settings';
    
    /**
     * Override the content property to include our custom layout
     */
    protected function getHeaderWidgets(): array
    {
        return [];
    }
    
    protected function getFooterWidgets(): array
    {
        return [];
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview_theme')
                ->label('Temayı Önizle')
                ->icon('heroicon-o-eye')
                ->url(fn (): string => route('home'), true)
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
    
    protected static ?int $navigationSort = 80;
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->loadSettings();
    }
    
    /**
     * Livewire bileşeninden gelen renkleri forma uygula
     */
    public function handleApplyColorsToForm()
    {
        $this->dispatch('theme-manager-colors-updated');
        
        Notification::make()
            ->title('Renkler forma uygulandı')
            ->success()
            ->send();
    }
    
    private function loadSettings(): void
    {
        $settings = ThemeSetting::all()->pluck('value', 'key')->toArray();
        $this->data = $settings;
        
        $this->form->fill($settings);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('settings_tabs')
                    ->tabs([
                        Tab::make('Genel')
                            ->icon('heroicon-o-cog')
                            ->schema($this->getGeneralTabSchema()),
                        
                        Tab::make('Renkler')
                            ->icon('heroicon-o-swatch')
                            ->schema($this->getColorsTabSchema()),
                        
                        Tab::make('Logo ve Görseller')
                            ->icon('heroicon-o-photo')
                            ->schema($this->getLogosTabSchema()),
                        
                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema($this->getSeoTabSchema()),
                            
                        Tab::make('Sosyal Medya')
                            ->icon('heroicon-o-share')
                            ->schema($this->getSocialTabSchema()),
                            
                        Tab::make('İletişim')
                            ->icon('heroicon-o-envelope')
                            ->schema($this->getContactTabSchema()),
                            
                        Tab::make('Yazı Tipi')
                            ->icon('heroicon-o-document-text')
                            ->schema($this->getTypographyTabSchema()),
                    ])
                    ->persistTabInQueryString()
            ])
            ->statePath('data');
    }
    
    private function getGeneralTabSchema(): array
    {
        return [
            Section::make('Site Bilgileri')
                ->schema([
                    TextInput::make('site_name')
                        ->label('Site Adı')
                        ->helperText('Sitenin genel başlığı ve tarayıcı sekmesinde görünecek isim')
                        ->required(),
                        
                    TextInput::make('site_description')
                        ->label('Site Açıklaması')
                        ->helperText('Sitenin kısa açıklaması')
                        ->required(),
                ]),
                
            Section::make('Ana Sayfa')
                ->schema([
                    RichEditor::make('home_hero_title')
                        ->label('Ana Sayfa Başlığı')
                        ->helperText('Ana sayfadaki büyük başlık metni')
                        ->required()
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ]),
                        
                    RichEditor::make('home_hero_subtitle')
                        ->label('Ana Sayfa Alt Başlığı')
                        ->helperText('Ana sayfadaki büyük başlığın altında yer alan açıklama metni')
                        ->required()
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ]),
                        
                    Toggle::make('show_search_box')
                        ->label('Arama Kutusunu Göster')
                        ->helperText('Ana sayfada rezervasyon arama kutusunu göster')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ]),
                
            Section::make('Popüler İçerikler')
                ->collapsible()
                ->schema([
                    Toggle::make('show_featured_hotels')
                        ->label('Öne Çıkan Otelleri Göster')
                        ->helperText('Ana sayfada öne çıkan otelleri göster')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                        
                    TextInput::make('featured_hotels_count')
                        ->label('Öne Çıkan Otel Sayısı')
                        ->helperText('Ana sayfada kaç tane öne çıkan otel gösterilecek')
                        ->numeric()
                        ->default(6)
                        ->minValue(3)
                        ->maxValue(12)
                        ->step(3),
                        
                    Toggle::make('show_featured_regions')
                        ->label('Öne Çıkan Bölgeleri Göster')
                        ->helperText('Ana sayfada öne çıkan bölgeleri göster')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ])
        ];
    }
    
    private function getColorsTabSchema(): array
    {
        return [
            Section::make('Renk Paleti Seçimi')
                ->description('Hazır bir renk paleti seçin veya kendi renklerinizi ayarlayın')
                ->schema([
                    Select::make('color_palette_selection')
                        ->label('Hazır Palet')
                        ->options(ColorPaletteService::getPalettesForSelect())
                        ->default(ColorPaletteService::getDefaultPaletteKey())
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (empty($state)) return;
                            
                            $paletteColors = ColorPaletteService::getPalette($state);
                            
                            if (!$paletteColors) return;
                            
                            foreach ($paletteColors as $colorKey => $colorValue) {
                                $set('color_' . $colorKey, $colorValue);
                            }
                        })
                        ->selectablePlaceholder(false),
                ]),
                
            Grid::make(3)->schema([
                Section::make('Ana Renkler')
                    ->schema([
                        ColorPicker::make('color_primary')
                            ->label('Ana Renk')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_secondary')
                            ->label('İkincil Renk')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_accent')
                            ->label('Vurgu Rengi')
                            ->required()
                            ->rgba(),
                    ]),
                    
                Section::make('Durum Renkleri')
                    ->schema([
                        ColorPicker::make('color_success')
                            ->label('Başarı')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_warning')
                            ->label('Uyarı')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_danger')
                            ->label('Tehlike')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_info')
                            ->label('Bilgi')
                            ->required()
                            ->rgba(),
                    ]),
                    
                Section::make('Metin Renkleri')
                    ->schema([
                        ColorPicker::make('color_text_primary')
                            ->label('Ana Metin')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_text_secondary')
                            ->label('İkincil Metin')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_text_muted')
                            ->label('Soluk Metin')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_background')
                            ->label('Sayfa Arkaplanı')
                            ->required()
                            ->rgba(),
                            
                        ColorPicker::make('color_border')
                            ->label('Kenarlık')
                            ->required()
                            ->rgba(),
                    ]),
            ]),
            
            // Header ve Footer bölümleri için özel bir seksiyon
            Section::make('Header ve Footer')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Group::make([
                                ColorPicker::make('color_header_bg')
                                    ->label('Header Arkaplan')
                                    ->required()
                                    ->rgba(),
                                    
                                ColorPicker::make('color_header_text')
                                    ->label('Header Metin')
                                    ->required()
                                    ->rgba(),
                            ]),
                                
                            Group::make([
                                ColorPicker::make('color_footer_bg')
                                    ->label('Footer Arkaplan')
                                    ->required()
                                    ->rgba(),
                                    
                                ColorPicker::make('color_footer_text')
                                    ->label('Footer Metin')
                                    ->required()
                                    ->rgba(),
                            ]),
                        ]),
                ]),
        ];
    }
    
    private function getLogosTabSchema(): array
    {
        return [
            Section::make('Logo Ayarları')
                ->description('Sitenizin logolarını buradan yönetin. Tüm görseller şeffaf PNG formatında yüklenmelidir.')
                ->schema([
                    FileUpload::make('logo_light')
                        ->label('Açık Arkaplan İçin Logo')
                        ->helperText('Beyaz/açık arkaplan üzerinde kullanılacak logo (PNG veya SVG önerilir)')
                        ->image()
                        ->directory('b2c-theme/images')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->imagePreviewHeight('100')
                        ->maxSize(1024)
                        ->acceptedFileTypes(['image/png', 'image/svg+xml'])
                        ->columnSpanFull(),
                        
                    FileUpload::make('logo_dark')
                        ->label('Koyu Arkaplan İçin Logo')
                        ->helperText('Siyah/koyu arkaplan üzerinde kullanılacak logo (PNG veya SVG önerilir)')
                        ->image()
                        ->directory('b2c-theme/images')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->imagePreviewHeight('100')
                        ->maxSize(1024)
                        ->acceptedFileTypes(['image/png', 'image/svg+xml'])
                        ->columnSpanFull(),
                        
                    FileUpload::make('favicon')
                        ->label('Favicon')
                        ->helperText('Site favicon\'u (ICO, PNG veya SVG formatında)')
                        ->image()
                        ->directory('b2c-theme/images')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->imagePreviewHeight('50')
                        ->maxSize(512)
                        ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/svg+xml']),
                ]),
                
            Section::make('Arka Plan Görseleri')
                ->schema([
                    FileUpload::make('hero_background')
                        ->label('Ana Sayfa Hero Arkaplanı')
                        ->helperText('Ana sayfada kullanılacak büyük arkaplan görüntüsü (1920x1080 veya daha büyük)')
                        ->image()
                        ->directory('b2c-theme/images/backgrounds')
                        ->visibility('public')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('16:9')
                        ->imageResizeTargetWidth('1920')
                        ->imageResizeTargetHeight('1080')
                        ->columnSpanFull(),
                ])
        ];
    }
    
    private function getSeoTabSchema(): array
    {
        return [
            Section::make('SEO Ayarları')
                ->schema([
                    Textarea::make('seo_meta_description')
                        ->label('Meta Açıklama')
                        ->helperText('Arama motorlarında görünecek site açıklaması (150-160 karakter)')
                        ->required()
                        ->rows(3)
                        ->maxLength(160),
                        
                    TextInput::make('seo_meta_keywords')
                        ->label('Meta Anahtar Kelimeler')
                        ->helperText('Virgülle ayrılmış anahtar kelimeler')
                        ->required(),
                        
                    FileUpload::make('seo_og_image')
                        ->label('Sosyal Medya Paylaşım Görseli')
                        ->helperText('Sosyal medyada link paylaşıldığında görünecek görsel (1200x630 önerilir)')
                        ->image()
                        ->directory('b2c-theme/images')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->imageCropAspectRatio('1200:630')
                        ->columnSpanFull(),
                        
                    Select::make('seo_twitter_card')
                        ->label('Twitter Kart Tipi')
                        ->options([
                            'summary' => 'Özet',
                            'summary_large_image' => 'Büyük Görsel ile Özet',
                        ])
                        ->default('summary_large_image')
                        ->required(),
                ]),
                
            Section::make('Gelişmiş SEO Ayarları')
                ->collapsed()
                ->schema([
                    Toggle::make('seo_enable_schema')
                        ->label('Yapısal Verileri Etkinleştir')
                        ->helperText('Schema.org yapısal verilerini etkinleştir (Google için)')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                    
                    Select::make('seo_schema_type')
                        ->label('Yapısal Veri Tipi')
                        ->options([
                            'Organization' => 'Organizasyon',
                            'LocalBusiness' => 'Yerel İşletme',
                            'Hotel' => 'Otel',
                            'TravelAgency' => 'Seyahat Acentası',
                        ])
                        ->default('TravelAgency')
                        ->visible(fn ($get) => $get('seo_enable_schema'))
                        ->required(),
                ])
        ];
    }
    
    private function getSocialTabSchema(): array
    {
        return [
            Section::make('Sosyal Medya Bağlantıları')
                ->schema([
                    TextInput::make('social_facebook')
                        ->label('Facebook')
                        ->helperText('Facebook profil veya sayfa linki')
                        ->url()
                        ->prefix('https://')
                        ->suffixIcon('heroicon-o-globe-alt'),
                        
                    TextInput::make('social_twitter')
                        ->label('Twitter')
                        ->helperText('Twitter/X profil linki')
                        ->url()
                        ->prefix('https://')
                        ->suffixIcon('heroicon-o-globe-alt'),
                        
                    TextInput::make('social_instagram')
                        ->label('Instagram')
                        ->helperText('Instagram profil linki')
                        ->url()
                        ->prefix('https://')
                        ->suffixIcon('heroicon-o-globe-alt'),
                        
                    TextInput::make('social_linkedin')
                        ->label('LinkedIn')
                        ->helperText('LinkedIn şirket profil linki')
                        ->url()
                        ->prefix('https://')
                        ->suffixIcon('heroicon-o-globe-alt'),
                ]),
                
            Section::make('Sosyal Medya Entegrasyonları')
                ->collapsed()
                ->schema([
                    Toggle::make('social_sharing_enabled')
                        ->label('Sosyal Medya Paylaşımlarını Etkinleştir')
                        ->helperText('Otel ve bölge sayfalarında sosyal medya paylaşım butonlarını göster')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                        
                    Toggle::make('social_feed_enabled')
                        ->label('Instagram Beslemesini Etkinleştir')
                        ->helperText('Ana sayfada Instagram beslemesini göster')
                        ->default(false)
                        ->onColor('success')
                        ->offColor('danger'),
                        
                    TextInput::make('social_feed_token')
                        ->label('Instagram Token')
                        ->helperText('Instagram beslemesi için erişim token\'ı')
                        ->visible(fn ($get) => $get('social_feed_enabled')),
                        
                    TextInput::make('social_feed_count')
                        ->label('Görüntülenecek Instagram Gönderisi Sayısı')
                        ->helperText('Ana sayfada kaç Instagram gönderisi gösterilecek')
                        ->visible(fn ($get) => $get('social_feed_enabled'))
                        ->numeric()
                        ->default(6)
                        ->minValue(3)
                        ->maxValue(12),
                ])
        ];
    }
    
    private function getContactTabSchema(): array
    {
        return [
            Section::make('İletişim Bilgileri')
                ->schema([
                    TextInput::make('contact_email')
                        ->label('E-posta Adresi')
                        ->email()
                        ->required()
                        ->suffixIcon('heroicon-o-envelope'),
                        
                    TextInput::make('contact_phone')
                        ->label('Telefon Numarası')
                        ->tel()
                        ->required()
                        ->suffixIcon('heroicon-o-phone'),
                        
                    Textarea::make('contact_address')
                        ->label('Adres')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
                
            Section::make('Harita Ayarları')
                ->collapsed()
                ->schema([
                    Toggle::make('contact_show_map')
                        ->label('İletişim Sayfasında Harita Göster')
                        ->helperText('İletişim sayfasında Google Harita göster')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                        
                    TextInput::make('contact_map_lat')
                        ->label('Harita Enlem (Latitude)')
                        ->helperText('Konumunuzun enlem değeri')
                        ->visible(fn ($get) => $get('contact_show_map')),
                        
                    TextInput::make('contact_map_lng')
                        ->label('Harita Boylam (Longitude)')
                        ->helperText('Konumunuzun boylam değeri')
                        ->visible(fn ($get) => $get('contact_show_map')),
                        
                    TextInput::make('contact_map_zoom')
                        ->label('Harita Zoom Seviyesi')
                        ->helperText('Haritanın zoom seviyesi (1-20 arası)')
                        ->visible(fn ($get) => $get('contact_show_map'))
                        ->numeric()
                        ->default(14)
                        ->minValue(1)
                        ->maxValue(20),
                ]),
        ];
    }
    
    private function getTypographyTabSchema(): array
    {
        return [
            Section::make('Yazı Tipi Ayarları')
                ->schema([
                    Select::make('typography_heading_font')
                        ->label('Başlık Yazı Tipi')
                        ->options([
                            'Inter, sans-serif' => 'Inter',
                            'Roboto, sans-serif' => 'Roboto',
                            'Open Sans, sans-serif' => 'Open Sans',
                            'Montserrat, sans-serif' => 'Montserrat',
                            'Poppins, sans-serif' => 'Poppins',
                            'Raleway, sans-serif' => 'Raleway',
                            'Playfair Display, serif' => 'Playfair Display',
                            'Merriweather, serif' => 'Merriweather',
                        ])
                        ->required()
                        ->reactive(),
                        
                    Placeholder::make('typography_heading_preview')
                        ->label('Başlık Yazı Tipi Önizleme')
                        ->content(function (callable $get): string {
                            $fontFamily = $get('typography_heading_font');
                            return view('theme-manager::components.font-preview', [
                                'fontFamily' => $fontFamily,
                                'sampleText' => 'Bu bir başlık yazı tipi örneğidir',
                                'sizes' => [
                                    'text-2xl' => 'Büyük Başlık',
                                    'text-xl' => 'Orta Başlık',
                                    'text-lg' => 'Küçük Başlık'
                                ]
                            ])->render();
                        }),
                        
                    Select::make('typography_body_font')
                        ->label('İçerik Yazı Tipi')
                        ->options([
                            'Inter, sans-serif' => 'Inter',
                            'Roboto, sans-serif' => 'Roboto',
                            'Open Sans, sans-serif' => 'Open Sans',
                            'Montserrat, sans-serif' => 'Montserrat',
                            'Poppins, sans-serif' => 'Poppins',
                            'Raleway, sans-serif' => 'Raleway',
                            'Source Sans Pro, sans-serif' => 'Source Sans Pro',
                            'Nunito, sans-serif' => 'Nunito',
                        ])
                        ->required()
                        ->reactive(),
                        
                    Placeholder::make('typography_body_preview')
                        ->label('İçerik Yazı Tipi Önizleme')
                        ->content(function (callable $get): string {
                            $fontFamily = $get('typography_body_font');
                            return view('theme-manager::components.font-preview', [
                                'fontFamily' => $fontFamily,
                                'sampleText' => 'Bu bir içerik yazı tipi örneğidir. İçerik metinleri genellikle bu yazı tipi ile gösterilir. Uzun metinlerde okunabilirlik çok önemlidir.',
                                'sizes' => [
                                    'text-base' => 'Normal Metin',
                                    'text-sm' => 'Küçük Metin',
                                    'text-xs' => 'Çok Küçük Metin'
                                ]
                            ])->render();
                        }),
                ]),
                
            Section::make('Gelişmiş Tipografi Ayarları')
                ->collapsed()
                ->schema([
                    Toggle::make('typography_use_custom_css')
                        ->label('Özel CSS Kullan')
                        ->helperText('Yazı tipleri için özel CSS kodları kullan')
                        ->default(false)
                        ->onColor('success')
                        ->offColor('danger'),
                        
                    Textarea::make('typography_custom_css')
                        ->label('Özel CSS Kodları')
                        ->helperText('Yazı tipleri için özel CSS kodlarınızı buraya girin')
                        ->visible(fn ($get) => $get('typography_use_custom_css'))
                        ->rows(10)
                        ->columnSpanFull(),
                ]),
        ];
    }
    
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->submit(),
            
            Action::make('reset')
                ->label('Varsayılanlara Sıfırla')
                ->color('danger')
                ->action(function () {
                    // ThemeSetting verilerini temizle
                    ThemeSetting::truncate();
                    
                    // Varsayılan ayarları yükle
                    app('theme.manager')->initializeDefaults();
                    
                    // Yeniden yükle
                    $this->loadSettings();
                    
                    Notification::make()
                        ->title('Ayarlar varsayılanlara döndürüldü')
                        ->success()
                        ->send();
                }),
        ];
    }
    
    public function save(): void
    {
        $themeManager = app('theme.manager');
        
        // Process file uploads
        foreach ($this->data as $key => $value) {
            if ($value instanceof TemporaryUploadedFile) {
                // Store each uploaded file
                $path = $value->store('public/b2c-theme/images');
                $url = Storage::url($path);
                
                // Update the value to the file URL
                $this->data[$key] = $url;
            }
        }
        
        // Save all settings
        foreach ($this->data as $key => $value) {
            // Determine the type and group based on key prefix
            $type = 'string';
            $group = 'general';
            
            if (str_starts_with($key, 'color_') || str_contains($key, '_color')) {
                $type = 'color';
                $group = 'colors';
            } elseif (str_starts_with($key, 'seo_')) {
                $group = 'seo';
            } elseif (str_starts_with($key, 'social_')) {
                $group = 'social';
            } elseif (str_starts_with($key, 'contact_')) {
                $group = 'contact';
            } elseif (str_starts_with($key, 'typography_')) {
                $group = 'typography';
            } elseif (str_starts_with($key, 'logo_') || $key === 'favicon' || str_contains($key, '_image')) {
                $group = 'logos';
                $type = 'image';
            }
            
            $themeManager->set($key, $value, $type, $group);
        }
        
        // Clear any cached settings
        $themeManager->clearCache();
        
        // Display success notification
        Notification::make()
            ->title('Tema ayarları kaydedildi')
            ->success()
            ->send();
    }
}