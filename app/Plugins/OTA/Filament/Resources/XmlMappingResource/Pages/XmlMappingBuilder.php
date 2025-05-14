<?php

namespace App\Plugins\OTA\Filament\Resources\XmlMappingResource\Pages;

use App\Plugins\OTA\Filament\Resources\XmlMappingResource;
use App\Plugins\OTA\Models\XmlMapping;
use App\Plugins\OTA\Services\XmlParserService;
use App\Plugins\OTA\Services\DataMappingService;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class XmlMappingBuilder extends Page
{
    use WithFileUploads;
    
    protected static string $resource = XmlMappingResource::class;
    
    protected static string $view = 'ota::pages.xml-mapping-builder';
    
    public $mapping;
    public $xmlFile;
    public $xmlContent;
    public $xmlPaths = [];
    public $systemFields = [];
    public $mappingData = [];
    
    protected $xmlParserService;
    protected DataMappingService $mappingService;

    public function mount(XmlMapping $record)
    {
        $this->mapping = $record;
        $this->mappingData = $record->mapping_data;
        
        $this->xmlParserService = new XmlParserService();
        $this->mappingService = app(DataMappingService::class);
        
        $this->systemFields = $this->mappingService->getSystemFields($record->mapping_entity);
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Action::make('saveMapping')
                ->label('Değişiklikleri Kaydet')
                ->color('success')
                ->action(function () {
                    $this->mapping->mapping_data = $this->mappingData;
                    $this->mapping->save();
                    
                    Notification::make()
                        ->title('Eşleştirme başarıyla güncellendi')
                        ->success()
                        ->send();
                }),
        ];
    }
    
    protected function getFormSchema(): array
    {
        return [
            Section::make('XML Analizi')
                ->schema([
                    FileUpload::make('xmlFile')
                        ->label('XML Dosyası Yükle')
                        ->disk('public')
                        ->directory('temp/xml')
                        ->acceptedFileTypes(['text/xml', 'application/xml'])
                        ->reactive(),
                        
                    Textarea::make('xmlContent')
                        ->label('veya XML içeriğini yapıştırın')
                        ->placeholder('XML içeriğini buraya yapıştırın')
                        ->rows(6),
                        
                    Placeholder::make('parseButton')
                        ->content(function () {
                            return <<<HTML
                            <button type="button" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action" wire:click="parseXml">
                                XML İçeriğini Analiz Et
                            </button>
                            HTML;
                        }),
                ]),
                
            Section::make('Mevcut Eşleştirme')
                ->schema([
                    Placeholder::make('currentMapping')
                        ->label('Mevcut Eşleştirme')
                        ->content(function () {
                            $json = json_encode($this->mappingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            return <<<HTML
                            <pre class="p-4 bg-gray-100 rounded overflow-auto max-h-96">{$json}</pre>
                            HTML;
                        }),
                ]),
                
            Section::make('XML Alanları')
                ->schema([
                    Placeholder::make('xmlPathsResult')
                        ->label('XML Alanları')
                        ->content(function () {
                            if (empty($this->xmlPaths)) {
                                return '<div class="text-center text-gray-500">XML analizi yapılmadı. Eşleştirme içeriğini düzenlemek için önce XML içeriğini analiz edin.</div>';
                            }
                            
                            $html = '<div class="overflow-auto max-h-96">';
                            $html .= '<table class="min-w-full divide-y divide-gray-200">';
                            $html .= '<thead class="bg-gray-50"><tr>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">XML Alanı</th>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tür</th>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Örnek Değer</th>';
                            $html .= '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>';
                            $html .= '</tr></thead>';
                            $html .= '<tbody class="bg-white divide-y divide-gray-200">';
                            
                            foreach ($this->xmlPaths as $path) {
                                $html .= '<tr>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . $path['path'] . '</td>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . ucfirst($path['type']) . '</td>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($path['example'] ?? '') . '</td>';
                                $html .= '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">';
                                $html .= '<button type="button" class="text-indigo-600 hover:text-indigo-900" wire:click="addMapping(\'' . $path['path'] . '\')">Eşleştir</button>';
                                $html .= '</td>';
                                $html .= '</tr>';
                            }
                            
                            $html .= '</tbody></table></div>';
                            return $html;
                        }),
                ]),
        ];
    }
    
    public function parseXml()
    {
        if ($this->xmlFile) {
            $path = Storage::disk('public')->path($this->xmlFile);
            $xmlContent = file_get_contents($path);
        } else {
            $xmlContent = $this->xmlContent;
        }
        
        if (empty($xmlContent)) {
            Notification::make()
                ->title('XML içeriği boş!')
                ->warning()
                ->send();
            return;
        }
        
        try {
            $this->xmlPaths = $this->xmlParserService->getExampleData($xmlContent);
            
            if (isset($this->xmlPaths['error'])) {
                Notification::make()
                    ->title($this->xmlPaths['error'])
                    ->danger()
                    ->send();
                return;
            }
            
            Notification::make()
                ->title('XML başarıyla analiz edildi')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('XML analizi sırasında hata: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function addMapping($xmlPath)
    {
        $this->js('window.dispatchEvent(new CustomEvent("open-mapping-modal", { detail: { xmlPath: "' . $xmlPath . '" } }))');
    }
    
    public function saveFieldMapping($xmlPath, $systemField, $transformationOptions = null)
    {
        if (empty($systemField)) {
            unset($this->mappingData[$xmlPath]);
            
            Notification::make()
                ->title('Eşleştirme kaldırıldı')
                ->info()
                ->send();
                
            return;
        }
        
        if ($transformationOptions) {
            $mapping = ['target' => $systemField];
            
            foreach ($transformationOptions as $key => $value) {
                if (!empty($value)) {
                    $mapping[$key] = $value;
                }
            }
            
            $this->mappingData[$xmlPath] = $mapping;
        } else {
            $this->mappingData[$xmlPath] = $systemField;
        }
        
        Notification::make()
            ->title('Eşleştirme güncellendi')
            ->success()
            ->send();
    }
}