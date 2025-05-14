<?php

namespace App\Plugins\OTA\Filament\Pages;

use App\Plugins\OTA\Models\Channel;
use App\Plugins\OTA\Services\XmlParserService;
use App\Plugins\OTA\Services\DataMappingService;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class XmlMappingWizard extends Page
{
    use WithFileUploads;
    
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Eşleştirme Oluştur';
    protected static ?string $navigationGroup = 'OTA & Entegrasyonlar';
    protected static ?string $title = 'Yeni Eşleştirme Oluştur';
    
    // Rota konfigürasyonu
    protected static ?string $slug = 'xml-mapping-wizard';
    
    protected static string $view = 'ota::pages.xml-mapping-wizard';
    
    // Form data
    public $channelId = null;
    public $mappingName = '';
    public $mappingType = 'import';
    public $formatType = 'xml';
    public $mappingEntity = 'reservation';
    public $xmlContent = '';
    public $xmlFile = null;
    public $xmlPaths = [];
    public $systemFields = [];
    public $mappings = [];
    public $mappingJson = '';
    public $description = '';
    
    protected $xmlParserService;
    protected DataMappingService $mappingService;

    public function boot()
    {
        $this->xmlParserService = new XmlParserService();
        $this->mappingService = app(DataMappingService::class);
    }
    
    public function mount()
    {
        $this->systemFields = $this->mappingService->getSystemFields('reservation');
    }
    
    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('Adım 1: Temel Bilgiler')
                    ->schema([
                        Section::make('Eşleştirme Bilgileri')
                            ->schema([
                                Select::make('channelId')
                                    ->label('OTA Kanalı')
                                    ->options(Channel::pluck('name', 'id')->toArray())
                                    ->required()
                                    ->reactive(),
                                    
                                TextInput::make('mappingName')
                                    ->label('Eşleştirme Adı')
                                    ->required()
                                    ->maxLength(255),
                                    
                                Select::make('mappingType')
                                    ->label('İşlem Tipi')
                                    ->options([
                                        'import' => 'İçe Aktar (Dış Sistemden İçeri)',
                                        'export' => 'Dışa Aktar (Bizden Dış Sisteme)',
                                    ])
                                    ->default('import')
                                    ->required(),

                                Select::make('formatType')
                                    ->label('Format Tipi')
                                    ->options([
                                        'xml' => 'XML',
                                        'json' => 'JSON'
                                    ])
                                    ->default('xml')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        // Reset content when format changes
                                        $this->xmlContent = '';
                                        $this->xmlPaths = [];
                                    }),

                                Select::make('mappingEntity')
                                    ->label('Veri Tipi')
                                    ->options([
                                        'reservation' => 'Rezervasyon',
                                        'room' => 'Oda',
                                        'rate' => 'Fiyat',
                                        'availability' => 'Müsaitlik',
                                        'hotel' => 'Otel',
                                        'guest' => 'Misafir',
                                    ])
                                    ->default('reservation')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        $this->systemFields = $this->mappingService->getSystemFields($state);
                                    }),
                                    
                                Textarea::make('description')
                                    ->label('Açıklama')
                                    ->placeholder('Bu eşleştirme ile ilgili notlar')
                                    ->rows(3)
                                    ->maxLength(500),
                            ])
                            ->columns(2),
                    ]),
                    
                Step::make('Adım 2: XML Analizi')
                    ->schema([
                        Section::make(fn () => $this->formatType === 'xml' ? 'XML İçeriği' : 'JSON İçeriği')
                            ->schema([
                                Toggle::make('useFile')
                                    ->label(fn () => $this->formatType === 'xml' ? 'XML dosyasından yükle' : 'JSON dosyasından yükle')
                                    ->default(false)
                                    ->reactive(),

                                FileUpload::make('xmlFile')
                                    ->label(fn () => $this->formatType === 'xml' ? 'XML Dosyası' : 'JSON Dosyası')
                                    ->acceptedFileTypes([
                                        'text/xml', 'application/xml',
                                        'application/json', 'text/plain'
                                    ])
                                    ->maxSize(2048) // 2MB
                                    ->directory('temp/data')
                                    ->visible(fn ($get) => $get('useFile')),

                                Textarea::make('xmlContent')
                                    ->label(fn () => $this->formatType === 'xml' ? 'XML İçeriği' : 'JSON İçeriği')
                                    ->placeholder(fn () => $this->formatType === 'xml'
                                        ? 'XML içeriğini buraya yapıştırın'
                                        : 'JSON içeriğini buraya yapıştırın'
                                    )
                                    ->rows(10)
                                    ->visible(fn ($get) => !$get('useFile')),
                                    
                                \Filament\Forms\Components\Actions::make([
                                    \Filament\Forms\Components\Actions\Action::make('parseXmlContent')
                                        ->label(fn () => $this->formatType === 'xml' ? 'XML İçeriğini Analiz Et' : 'JSON İçeriğini Analiz Et')
                                        ->button()
                                        ->color('primary')
                                        ->action('parseXmlContent')
                                ]),
                            ]),
                            
                        Section::make(fn () => $this->formatType === 'xml' ? 'XML Analiz Sonuçları' : 'JSON Analiz Sonuçları')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('emptyPathsMessage')
                                    ->content(fn () => $this->formatType === 'xml'
                                        ? 'XML analizi yapılmadı. Lütfen önce XML içeriğini analiz edin.'
                                        : 'JSON analizi yapılmadı. Lütfen önce JSON içeriğini analiz edin.'
                                    )
                                    ->visible(fn () => empty($this->xmlPaths)),

                                \Filament\Forms\Components\ViewField::make('xmlPathsTable')
                                    ->label(fn () => $this->formatType === 'xml' ? 'XML Alanları' : 'JSON Alanları')
                                    ->view('ota::components.xml-paths-table')
                                    ->viewData([
                                        'xmlPaths' => fn () => $this->xmlPaths
                                    ])
                                    ->visible(fn () => !empty($this->xmlPaths)),
                            ]),
                    ]),
                    
                Step::make('Adım 3: Alan Eşleştirme')
                    ->schema([
                        Section::make('Eşleştirme Oluşturucu')
                            ->schema([
                                Repeater::make('mappings')
                                    ->label('')
                                    ->schema([
                                        Select::make('xmlPath')
                                            ->label('XML Alanı')
                                            ->options(function () {
                                                $options = [];
                                                foreach ($this->xmlPaths as $path) {
                                                    $options[$path['path']] = $path['path'] . ' (' . ($path['example'] ? 'Örnek: ' . $path['example'] : '') . ')';
                                                }
                                                return $options;
                                            })
                                            ->searchable()
                                            ->required(),
                                            
                                        Select::make('systemField')
                                            ->label('Sistem Alanı')
                                            ->options(function () {
                                                $options = [];
                                                foreach ($this->systemFields as $field => $details) {
                                                    $options[$field] = $field . ' (' . $details['description'] . ')';
                                                }
                                                return $options;
                                            })
                                            ->searchable()
                                            ->required(),
                                            
                                        Toggle::make('hasTransformation')
                                            ->label('Dönüşüm Gerekli')
                                            ->default(false)
                                            ->reactive(),
                                            
                                        Select::make('transformationType')
                                            ->label('Dönüşüm Tipi')
                                            ->options([
                                                'date_format' => 'Tarih Formatı',
                                                'currency' => 'Para Birimi',
                                                'boolean' => 'Boolean (Evet/Hayır)',
                                                'fixed' => 'Sabit Değer',
                                                'custom' => 'Özel Dönüşüm',
                                            ])
                                            ->visible(fn ($get) => $get('hasTransformation'))
                                            ->reactive(),
                                            
                                        Select::make('dateFormatting')
                                            ->label('Tarih Formatı Dönüşümü')
                                            ->options($this->mappingService->getTransformationOptions()['date_format'])
                                            ->visible(fn ($get) => $get('hasTransformation') && $get('transformationType') === 'date_format'),
                                            
                                        Select::make('currencyFrom')
                                            ->label('Kaynak Para Birimi')
                                            ->options($this->mappingService->getAvailableCurrencies())
                                            ->visible(fn ($get) => $get('hasTransformation') && $get('transformationType') === 'currency'),
                                            
                                        Select::make('currencyTo')
                                            ->label('Hedef Para Birimi')
                                            ->options($this->mappingService->getAvailableCurrencies())
                                            ->default('TRY')
                                            ->visible(fn ($get) => $get('hasTransformation') && $get('transformationType') === 'currency'),
                                            
                                        Select::make('booleanFormat')
                                            ->label('Boolean Formatı')
                                            ->options($this->mappingService->getTransformationOptions()['boolean'])
                                            ->visible(fn ($get) => $get('hasTransformation') && $get('transformationType') === 'boolean'),
                                            
                                        TextInput::make('fixedValue')
                                            ->label('Sabit Değer')
                                            ->visible(fn ($get) => $get('hasTransformation') && $get('transformationType') === 'fixed'),
                                            
                                        Textarea::make('customTransformation')
                                            ->label('Özel Dönüşüm Kodu')
                                            ->placeholder('PHP kodu, $value değişkeni giriş değeridir')
                                            ->rows(3)
                                            ->visible(fn ($get) => $get('hasTransformation') && $get('transformationType') === 'custom'),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => 
                                        $state['xmlPath'] ? "{$state['xmlPath']} → {$state['systemField']}" : null
                                    )
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->addActionLabel('Yeni Eşleştirme Ekle')
                                    ->columns(2),
                            ]),
                            
                        Section::make('Hızlı Eşleştirme')
                            ->schema([
                                Placeholder::make('quickMappingHelp')
                                    ->label('XML ve Sistem Alanlarını Hızlı Eşleştirme')
                                    ->content('Aynı ada sahip alanları otomatik olarak eşleştirmek için bu düğmeyi kullanın.')
                                    ->columnSpan(2),
                                    
                                \Filament\Forms\Components\Actions::make([
                                    \Filament\Forms\Components\Actions\Action::make('autoMapFields')
                                        ->label('Otomatik Eşleştir')
                                        ->button()
                                        ->color('primary')
                                        ->action('autoMapFields')
                                ])
                                ->columnSpan(2),
                            ])
                            ->columns(2),
                            
                        Section::make('Eşleştirme Önizleme')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('noPreviewMessage')
                                    ->content('Eşleştirme yapıldıktan sonra JSON önizleme burada görüntülenecektir.')
                                    ->visible(fn () => empty($this->mappingJson)),
                                    
                                \Filament\Forms\Components\ViewField::make('mappingPreview')
                                    ->label('JSON Önizleme')
                                    ->view('ota::components.code-preview')
                                    ->viewData([
                                        'code' => fn () => $this->mappingJson,
                                        'language' => 'json',
                                        'copyable' => true
                                    ])
                                    ->visible(fn () => !empty($this->mappingJson)),
                                    
                                \Filament\Forms\Components\Actions::make([
                                    \Filament\Forms\Components\Actions\Action::make('generateMappingJson')
                                        ->label('Eşleştirmeyi Önizle')
                                        ->button()
                                        ->color('primary')
                                        ->action('generateMappingJson')
                                ]),
                            ]),
                    ]),
                    
                Step::make('Adım 4: Kaydetme')
                    ->schema([
                        Section::make('Eşleştirme Özeti')
                            ->schema([
                                \Filament\Forms\Components\Placeholder::make('noMappingMessage')
                                    ->content('Eşleştirme bilgisi oluşturulmadı. Lütfen önceki adımda eşleştirmeyi önizleyin.')
                                    ->visible(fn () => empty(json_decode($this->mappingJson, true))),
                                    
                                \Filament\Forms\Components\ViewField::make('mappingSummary')
                                    ->label('Özet')
                                    ->view('ota::components.mapping-summary')
                                    ->viewData([
                                        'mappingData' => fn () => json_decode($this->mappingJson, true) ?? [],
                                        'mappingName' => fn () => $this->mappingName,
                                        'channelName' => fn () => Channel::find($this->channelId)->name ?? 'Bilinmiyor',
                                        'mappingType' => fn () => $this->mappingType === 'import' ? 'İçe Aktar' : 'Dışa Aktar',
                                        'mappingEntity' => fn () => ucfirst($this->mappingEntity),
                                        'totalMappings' => function () {
                                            $mappingData = json_decode($this->mappingJson, true) ?? [];
                                            return count($mappingData);
                                        },
                                        'transformationCount' => function () {
                                            $mappingData = json_decode($this->mappingJson, true) ?? [];
                                            $count = 0;
                                            foreach ($mappingData as $field => $value) {
                                                if (is_array($value)) {
                                                    $count++;
                                                }
                                            }
                                            return $count;
                                        },
                                        'simpleMappingCount' => function () {
                                            $mappingData = json_decode($this->mappingJson, true) ?? [];
                                            $transformationCount = 0;
                                            foreach ($mappingData as $field => $value) {
                                                if (is_array($value)) {
                                                    $transformationCount++;
                                                }
                                            }
                                            return count($mappingData) - $transformationCount;
                                        }
                                    ])
                                    ->visible(fn () => !empty(json_decode($this->mappingJson, true))),
                            ]),
                            
                        Section::make('Eşleştirmeyi Kaydet')
                            ->schema([
                                \Filament\Forms\Components\Actions::make([
                                    \Filament\Forms\Components\Actions\Action::make('saveMappingConfiguration')
                                        ->label('Eşleştirmeyi Kaydet')
                                        ->button()
                                        ->color('success')
                                        ->size('lg')
                                        ->extraAttributes(['class' => 'w-full'])
                                        ->action('saveMappingConfiguration')
                                ]),
                            ]),
                    ]),
            ])
                ->skippable()
                ->persistStepInQueryString('step'),
        ];
    }

    // Methodlar
    
    public function parseXmlContent()
    {
        // Determine content from file or textarea
        if ($this->xmlFile) {
            $path = Storage::disk('public')->path($this->xmlFile);
            $content = file_get_contents($path);
        } else {
            $content = $this->xmlContent;
        }

        if (empty($content)) {
            Notification::make()
                ->title($this->formatType === 'xml' ? 'XML içeriği boş!' : 'JSON içeriği boş!')
                ->warning()
                ->send();
            return;
        }

        try {
            if ($this->formatType === 'xml') {
                // Use XML parser
                $this->xmlPaths = $this->xmlParserService->getExampleData($content);
            } else {
                // Use JSON parser (we assume both service methods handle their formats accordingly)
                $this->xmlPaths = $this->xmlParserService->getJsonExampleData($content);
            }

            if (isset($this->xmlPaths['error'])) {
                Notification::make()
                    ->title($this->xmlPaths['error'])
                    ->danger()
                    ->send();
                return;
            }

            Notification::make()
                ->title($this->formatType === 'xml'
                    ? 'XML başarıyla analiz edildi'
                    : 'JSON başarıyla analiz edildi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title($this->formatType === 'xml'
                    ? 'XML analizi sırasında hata: ' . $e->getMessage()
                    : 'JSON analizi sırasında hata: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function autoMapFields()
    {
        if (empty($this->xmlPaths)) {
            Notification::make()
                ->title('Önce XML içeriğini analiz edin')
                ->warning()
                ->send();
            return;
        }
        
        $systemFieldKeys = array_keys($this->systemFields);
        $autoMappings = [];
        
        foreach ($this->xmlPaths as $xmlPath) {
            $pathParts = explode('.', $xmlPath['path']);
            $lastPart = end($pathParts);
            
            // İsimleri eşleştir
            foreach ($systemFieldKeys as $systemField) {
                $fieldParts = explode('.', $systemField);
                $lastFieldPart = end($fieldParts);
                
                // Tam eşleşme kontrolü
                if (strtolower($lastPart) === strtolower($lastFieldPart)) {
                    $autoMappings[] = [
                        'xmlPath' => $xmlPath['path'],
                        'systemField' => $systemField,
                        'hasTransformation' => false,
                        'transformationType' => null,
                    ];
                    break;
                }
                
                // Benzer isim kontrolü (örn: roomType -> room_type)
                $normalizedXmlPart = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $lastPart));
                $normalizedSystemPart = strtolower($lastFieldPart);
                
                if ($normalizedXmlPart === $normalizedSystemPart) {
                    $autoMappings[] = [
                        'xmlPath' => $xmlPath['path'],
                        'systemField' => $systemField,
                        'hasTransformation' => false,
                        'transformationType' => null,
                    ];
                    break;
                }
            }
        }
        
        if (empty($autoMappings)) {
            Notification::make()
                ->title('Otomatik eşleştirilebilecek alan bulunamadı')
                ->warning()
                ->send();
            return;
        }
        
        $this->mappings = $autoMappings;
        
        Notification::make()
            ->title(count($autoMappings) . ' alan otomatik eşleştirildi')
            ->success()
            ->send();
            
        // Hemen JSON önizleme oluştur
        $this->generateMappingJson();
    }
    
    public function generateMappingJson()
    {
        if (empty($this->mappings)) {
            Notification::make()
                ->title('Eşleştirme yapmadan önce en az bir alan eşleştirin')
                ->warning()
                ->send();
            return;
        }
        
        $mappingData = [];
        
        foreach ($this->mappings as $mapping) {
            $xmlPath = $mapping['xmlPath'];
            $systemField = $mapping['systemField'];
            
            if ($mapping['hasTransformation']) {
                $target = ['target' => $systemField];
                
                switch ($mapping['transformationType']) {
                    case 'date_format':
                        $target['format'] = $mapping['dateFormatting'] ?? null;
                        break;
                        
                    case 'currency':
                        $target['currency'] = [
                            'from' => $mapping['currencyFrom'] ?? 'USD',
                            'to' => $mapping['currencyTo'] ?? 'TRY'
                        ];
                        break;
                        
                    case 'boolean':
                        $target['boolean_format'] = $mapping['booleanFormat'] ?? null;
                        break;
                        
                    case 'fixed':
                        $target['fixed'] = $mapping['fixedValue'];
                        break;
                        
                    case 'custom':
                        $target['custom'] = $mapping['customTransformation'];
                        break;
                }
                
                $mappingData[$xmlPath] = $target;
            } else {
                $mappingData[$xmlPath] = $systemField;
            }
        }
        
        // Sabit değer atamaları
        if (isset($mapping['fixedValue']) && $mapping['transformationType'] === 'fixed') {
            $mappingData[$systemField] = [
                'fixed' => $mapping['fixedValue']
            ];
        }
        
        $this->mappingJson = json_encode($mappingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        Notification::make()
            ->title('Eşleştirme önizlemesi oluşturuldu')
            ->success()
            ->send();
    }
    
    public function saveMappingConfiguration()
    {
        if (empty($this->mappingJson)) {
            Notification::make()
                ->title('Kaydetmeden önce eşleştirme önizlemesi oluşturun')
                ->warning()
                ->send();
            return;
        }
        
        $channel = Channel::find($this->channelId);
        
        if (!$channel) {
            Notification::make()
                ->title('OTA kanalı bulunamadı')
                ->danger()
                ->send();
            return;
        }
        
        try {
            $mappingData = json_decode($this->mappingJson, true);
            
            $mapping = $this->mappingService->saveMapping(
                $channel,
                $this->mappingEntity,
                $this->mappingName,
                $mappingData,
                $this->mappingType,
                $this->description,
                $this->formatType // Add format_type parameter
            );
            
            Notification::make()
                ->title('Eşleştirme başarıyla kaydedildi')
                ->success()
                ->send();
                
            // Yönlendirme
            return redirect()->route('filament.admin.resources.xml-mappings.edit', ['record' => $mapping->id]);
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Kayıt sırasında hata: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}