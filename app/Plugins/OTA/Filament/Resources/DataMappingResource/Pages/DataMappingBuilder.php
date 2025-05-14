<?php

namespace App\Plugins\OTA\Filament\Resources\DataMappingResource\Pages;

use App\Plugins\OTA\Filament\Resources\DataMappingResource;
use App\Plugins\OTA\Models\DataMapping;
use App\Plugins\OTA\Services\DataMappingService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;

class DataMappingBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = DataMappingResource::class;

    protected static string $view = 'ota::pages.data-mapping-builder';
    
    public ?array $data = [];
    
    public DataMapping $record;
    
    public ?string $sampleData = null;
    
    public ?array $parsedPaths = [];
    
    public ?array $systemFields = [];
    
    protected DataMappingService $mappingService;
    
    public function __construct()
    {
        $this->mappingService = app(DataMappingService::class);
    }

    public function mount(DataMapping $record): void
    {
        $this->record = $record;
        $this->form->fill([
            'mapping_data' => $record->mapping_data,
            'sample_data' => '',
        ]);
        
        // Get system fields based on entity type
        $this->systemFields = $this->mappingService->getSystemFields($record->mapping_entity);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sample Data')
                    ->schema([
                        Textarea::make('sample_data')
                            ->label('Sample Data')
                            ->placeholder("Paste sample data in " . strtoupper($this->record->format_type) . " format to analyze")
                            ->rows(10)
                            ->required(),
                            
                        FileUpload::make('sample_file')
                            ->label('Or upload a file')
                            ->directory('uploads/temp')
                            ->maxSize(5120), // 5MB
                            
                        ViewField::make('code_preview')
                            ->view('ota::components.code-preview')
                            ->viewData([
                                'code' => fn() => $this->sampleData,
                                'language' => fn() => $this->record->format_type,
                                'copyable' => true,
                            ])
                            ->visible(fn() => !empty($this->sampleData)),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }
    
    public function analyzeSampleData(): void
    {
        $this->validate();
        
        $this->sampleData = $this->data['sample_data'] ?? '';
        
        if (empty($this->sampleData) && isset($this->data['sample_file'])) {
            // Read from uploaded file
            $filePath = Storage::disk('public')->path($this->data['sample_file']);
            $this->sampleData = file_get_contents($filePath);
        }
        
        if (empty($this->sampleData)) {
            Notification::make()
                ->title('Please provide sample data')
                ->danger()
                ->send();
            return;
        }
        
        // Detect and use the appropriate parser based on format_type
        $parser = $this->mappingService->getParser($this->record->format_type);
        
        if (!$parser) {
            Notification::make()
                ->title("No parser available for {$this->record->format_type} format")
                ->danger()
                ->send();
            return;
        }
        
        try {
            // Parse the sample data
            $this->parsedPaths = $parser->getExampleData($this->sampleData);
            
            Notification::make()
                ->title('Sample data analyzed successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error analyzing sample data')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public function generateMapping(): void
    {
        if (empty($this->parsedPaths)) {
            Notification::make()
                ->title('Please analyze sample data first')
                ->warning()
                ->send();
            return;
        }
        
        // Get the parser for the current format type
        $parser = $this->mappingService->getParser($this->record->format_type);
        
        if (!$parser) {
            Notification::make()
                ->title("No parser available for {$this->record->format_type} format")
                ->danger()
                ->send();
            return;
        }
        
        // Generate template mapping
        $mappingTemplate = $parser->generateMappingTemplate($this->parsedPaths);
        
        // Update the record
        $this->record->mapping_data = $mappingTemplate;
        $this->record->save();
        
        Notification::make()
            ->title('Mapping template generated successfully')
            ->success()
            ->send();
            
        // Redirect to edit page
        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record]));
    }
    
    /**
     * Maps a specific path from the parsed data to be used in the mapping
     *
     * @param string $path The path to map
     * @return void
     */
    public function mapPath(string $path): void
    {
        // This method is called when a user clicks the 'Map' button next to a parsed path
        // Add this path to the data structure that will be used in the mapping

        // You could display a modal, add to a list, or implement custom mapping logic here
        // For now, just notify the user
        Notification::make()
            ->title("Path '{$path}' selected for mapping")
            ->body("You can now map this path to a system field")
            ->success()
            ->send();

        // Get current mapping data (or initialize it)
        $mappingData = $this->record->mapping_data ?? [];

        // Add this path to mapping data if it doesn't exist already
        if (!isset($mappingData[$path])) {
            $mappingData[$path] = '';  // Set to empty value initially

            // Update the record with the new mapping data
            $this->record->mapping_data = $mappingData;
            $this->record->save();

            Notification::make()
                ->title("Added '{$path}' to mapping data")
                ->success()
                ->send();
        }
    }

    public function saveMapping(): void
    {
        // Validate mapping data
        if (empty($this->data['mapping_data'])) {
            Notification::make()
                ->title('Mapping data cannot be empty')
                ->danger()
                ->send();
            return;
        }

        // Update the record
        $this->record->mapping_data = $this->data['mapping_data'];
        $this->record->save();

        Notification::make()
            ->title('Mapping saved successfully')
            ->success()
            ->send();
    }
}