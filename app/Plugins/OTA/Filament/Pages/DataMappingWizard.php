<?php

namespace App\Plugins\OTA\Filament\Pages;

use App\Plugins\OTA\Models\Channel;
use App\Plugins\OTA\Models\DataMapping;
use App\Plugins\OTA\Services\DataMappingService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class DataMappingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'ota::pages.data-mapping-wizard';
    
    protected static ?string $navigationLabel = 'Data Mapping Wizard';
    
    protected static ?string $navigationGroup = 'OTA & Entegrasyonlar';
    
    protected static ?int $navigationSort = 10;
    
    protected static ?string $slug = 'ota/data-mapping-wizard';
    
    public ?array $data = [];

    public $currentStep = 1;

    protected DataMappingService $mappingService;

    public function mount(): void
    {
        $this->mappingService = app(DataMappingService::class);
        $this->form->fill();
    }

    protected function getMappingService(): DataMappingService
    {
        if (!isset($this->mappingService)) {
            $this->mappingService = app(DataMappingService::class);
        }
        return $this->mappingService;
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make()
                    ->columns(2) // İki sütun kullanarak genişliği artırıyoruz
                    ->startOnStep($this->currentStep)
                    ->hiddenLabel() // Label'ı gizliyoruz
                    ->submitAction(null) // Form submit butonunu gizliyoruz, kendi butonlarımızı kullanacağız
                    ->steps([
                        Step::make('Temel Bilgiler')
                            ->description('Eşleştirme için temel bilgileri girin')
                            ->schema([
                                Select::make('channel_id')
                                    ->label('OTA Kanalı')
                                    ->options(Channel::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(1),

                                TextInput::make('name')
                                    ->label('Eşleştirme Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                Select::make('operation_type')
                                    ->label('İşlem Tipi')
                                    ->options($this->getMappingService()->getAvailableOperations())
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('format_type')
                                    ->label('Format Tipi')
                                    ->options($this->getMappingService()->getAvailableFormats())
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('mapping_entity')
                                    ->label('Eşleştirme Varlığı')
                                    ->options([
                                        'room' => 'Oda',
                                        'rate' => 'Fiyat',
                                        'availability' => 'Müsaitlik',
                                        'reservation' => 'Rezervasyon',
                                        'hotel' => 'Otel',
                                        'guest' => 'Misafir',
                                    ])
                                    ->required()
                                    ->columnSpan(1),

                                Textarea::make('description')
                                    ->label('Açıklama')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpan(2),
                            ]),
                            
                        Step::make('Örnek Veri')
                            ->description('Dış sistemden örnek bir veri girin')
                            ->schema([
                                Textarea::make('sample_data')
                                    ->label('Örnek Veri')
                                    ->helperText('XML veya JSON formatında örnek bir veri parçası yapıştırın')
                                    ->rows(15)
                                    ->required()
                                    ->columnSpan(2), // Tam genişlik kullan
                            ]),
                            
                        Step::make('Doğrulama ve Oluşturma')
                            ->description('Eşleştirme kurallarını doğrulayın ve oluşturun')
                            ->schema([
                                // Bu adımda veriler doğrulanır ve özetlenir
                                // Burada bir özet gösterimi eklenecek
                            ]),
                    ])
            ])
            ->statePath('data');
    }
    
    public function create(): void
    {
        // Form verilerini doğrula
        $this->validate();
        
        try {
            // Verileri al
            $channelId = $this->data['channel_id'];
            $name = $this->data['name'];
            $operationType = $this->data['operation_type'];
            $formatType = $this->data['format_type'];
            $mappingEntity = $this->data['mapping_entity'];
            $description = $this->data['description'] ?? null;
            $sampleData = $this->data['sample_data'] ?? null;
            
            // Veri formatını kontrol et ve analiz et
            $parser = $this->getMappingService()->getParser($formatType);

            if (!$parser) {
                Notification::make()
                    ->title("$formatType formatı için desteklenmeyen bir parser.")
                    ->danger()
                    ->send();
                return;
            }
            
            if (!$parser->canParse($sampleData)) {
                Notification::make()
                    ->title("Örnek veri, seçilen formata uygun değil.")
                    ->danger()
                    ->send();
                return;
            }
            
            // Örnek veriyi analiz et
            $parsedPaths = $parser->getExampleData($sampleData);
            
            // Eşleştirme şablonu oluştur
            $mappingTemplate = $parser->generateMappingTemplate($parsedPaths);
            
            // Eşleştirmeyi veritabanına kaydet
            $channel = Channel::findOrFail($channelId);

            $mapping = $this->getMappingService()->saveMapping(
                $channel,
                $mappingEntity,
                $name,
                $mappingTemplate,
                $operationType,
                $formatType,
                $description
            );
            
            // Başarılı bildirim
            Notification::make()
                ->title('Eşleştirme başarıyla oluşturuldu')
                ->success()
                ->send();
                
            // Düzenleme sayfasına yönlendir
            $this->redirect(route('filament.admin.resources.data-mappings.edit', ['record' => $mapping->id]));
            
        } catch (\Exception $e) {
            // Hata bildirim
            Notification::make()
                ->title('Eşleştirme oluşturulamadı')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}