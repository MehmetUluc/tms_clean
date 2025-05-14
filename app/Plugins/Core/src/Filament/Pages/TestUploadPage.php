<?php

namespace App\Plugins\Core\src\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class TestUploadPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Dosya Yükleme Testi';
    
    protected static ?string $navigationGroup = 'Plugin Testi';
    
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.page';
    
    // Form modeli
    public ?array $data = [];
    
    public $galleryPreview = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Standart Dosya Yükleme')
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Kapak Resmi')
                            ->image()
                            ->disk('public')
                            ->directory('test-uploads')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->helperText('Bu standart bir Filament dosya yükleme bileşenidir'),
                    ]),
                    
                Section::make('Grid Dosya Yükleme')
                    ->schema([
                        FileUpload::make('gallery')
                            ->label('Galeri Resimleri')
                            ->image()
                            ->multiple()
                            ->disk('public')
                            ->directory('test-uploads/gallery')
                            ->visibility('public')
                            ->helperText('Standart Filament FileUpload bileşeni kullanılıyor'),
                    ]),
            ])
            ->statePath('data')
            ->live();
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        // Dosyaları ve önizlemeyi kaydedin
        $this->data = $data;
        
        if (isset($data['gallery']) && is_array($data['gallery'])) {
            // Geçici URL'lerden storage URL'lerine dönüştürün
            $this->galleryPreview = collect($data['gallery'])->map(function ($path) {
                return asset('storage/' . $path);
            })->toArray();
        }
        
        $this->notify('success', 'Dosyalar başarıyla yüklendi!');
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}