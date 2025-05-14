<?php

namespace App\Plugins\ThemeManager\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

class ColorPreview extends Component
{
    // Ana renkler
    public $primaryColor = '#3b82f6';
    public $secondaryColor = '#10b981';
    public $accentColor = '#8b5cf6';
    
    // Durum renkleri
    public $successColor = '#10b981';
    public $dangerColor = '#ef4444';
    public $warningColor = '#f59e0b';
    public $infoColor = '#3b82f6';
    
    // Metin renkleri
    public $textPrimaryColor = '#1f2937';
    public $textSecondaryColor = '#4b5563';
    public $textMutedColor = '#9ca3af';
    
    // Arkaplan ve kenar renkleri
    public $backgroundColor = '#ffffff';
    public $borderColor = '#e5e7eb';
    
    // Açık durum renkleri
    public $successLightColor = '#d1fae5';
    public $errorLightColor = '#fee2e2';
    public $warningLightColor = '#fef3c7';
    
    // Sayfa bölüm renkleri
    public $headerBgColor = '#1e40af';
    public $headerTextColor = '#ffffff';
    public $footerBgColor = '#1e293b';
    public $footerTextColor = '#f3f4f6';
    
    // Tema modu
    public $darkMode = false;
    
    // Aktif renk teması
    public $activeColorTheme = 'default';
    
    // Hazır tema paletleri
    protected $colorThemes = [
        'default' => [
            'name' => 'Varsayılan',
            'primaryColor' => '#3b82f6',
            'secondaryColor' => '#10b981', 
            'accentColor' => '#8b5cf6',
            'headerBgColor' => '#1e40af',
            'footerBgColor' => '#1e293b',
        ],
        'modern' => [
            'name' => 'Modern',
            'primaryColor' => '#6366f1',
            'secondaryColor' => '#ec4899', 
            'accentColor' => '#14b8a6',
            'headerBgColor' => '#4f46e5',
            'footerBgColor' => '#312e81',
        ],
        'warm' => [
            'name' => 'Sıcak Renkler',
            'primaryColor' => '#f97316',
            'secondaryColor' => '#f43f5e', 
            'accentColor' => '#eab308',
            'headerBgColor' => '#b45309',
            'footerBgColor' => '#78350f',
        ],
        'cool' => [
            'name' => 'Soğuk Renkler',
            'primaryColor' => '#0ea5e9',
            'secondaryColor' => '#0891b2', 
            'accentColor' => '#0d9488',
            'headerBgColor' => '#0c4a6e',
            'footerBgColor' => '#164e63',
        ],
        'luxury' => [
            'name' => 'Lüks',
            'primaryColor' => '#9333ea',
            'secondaryColor' => '#2563eb', 
            'accentColor' => '#c026d3',
            'headerBgColor' => '#581c87',
            'footerBgColor' => '#312e81',
        ],
        'nature' => [
            'name' => 'Doğal',
            'primaryColor' => '#16a34a',
            'secondaryColor' => '#65a30d', 
            'accentColor' => '#eab308',
            'headerBgColor' => '#166534',
            'footerBgColor' => '#365314',
        ],
        'monochrome' => [
            'name' => 'Monokrom',
            'primaryColor' => '#4b5563',
            'secondaryColor' => '#6b7280', 
            'accentColor' => '#374151',
            'headerBgColor' => '#1f2937',
            'footerBgColor' => '#111827',
        ],
    ];
    
    public function mount()
    {
        // Başlangıç değerleri gelecekte veritabanından yüklenebilir
    }
    
    /**
     * Hazır renk temaları listesini döndürür
     */
    public function getColorThemes()
    {
        return $this->colorThemes;
    }
    
    /**
     * Belirtilen renk temasını uygular
     */
    public function applyColorTheme($theme)
    {
        if (!isset($this->colorThemes[$theme])) {
            return;
        }
        
        $this->activeColorTheme = $theme;
        foreach ($this->colorThemes[$theme] as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
        
        // Durum renklerini ve metin renklerini akıllıca ayarla
        if ($theme === 'monochrome') {
            $this->successColor = '#4ade80';
            $this->dangerColor = '#f87171';
            $this->warningColor = '#fbbf24';
            $this->infoColor = '#60a5fa';
        } else {
            $this->successColor = '#10b981';
            $this->dangerColor = '#ef4444';
            $this->warningColor = '#f59e0b';
            $this->infoColor = '#3b82f6';
        }
        
        // Koyu mod ise metin renklerini uygun şekilde ayarla
        if ($this->darkMode) {
            $this->backgroundColor = '#1f2937';
            $this->borderColor = '#374151';
            $this->textPrimaryColor = '#f9fafb';
            $this->textSecondaryColor = '#e5e7eb';
            $this->textMutedColor = '#9ca3af';
            $this->headerTextColor = '#ffffff';
            $this->footerTextColor = '#f3f4f6';
        } else {
            $this->backgroundColor = '#ffffff';
            $this->borderColor = '#e5e7eb';
            $this->textPrimaryColor = '#1f2937';
            $this->textSecondaryColor = '#4b5563';
            $this->textMutedColor = '#9ca3af';
            $this->headerTextColor = '#ffffff';
            $this->footerTextColor = '#f3f4f6';
        }
    }
    
    /**
     * Koyu/açık modu değiştirir
     */
    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        $this->applyColorTheme($this->activeColorTheme);
    }
    
    /**
     * Kontrast renk üretme
     */
    public function generateContrastColor($hexColor) 
    {
        // HEX değerini RGB'ye çevir
        $hex = ltrim($hexColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Rengin parlaklığını hesapla (W3C formülü)
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        
        // Parlaklığa göre siyah veya beyaz döndür
        return ($brightness > 128) ? '#000000' : '#ffffff';
    }
    
    /**
     * Renkleri Filament formundan günceller
     */
    #[On('updateColor')]
    public function updateColor($name, $value)
    {
        // Direk property match kontrolü
        if (property_exists($this, $name)) {
            $this->$name = $value;
            return;
        }
        
        // color_primary -> primaryColor gibi dönüşümler için mapping
        $propertyMappings = [
            'primaryColor' => ['color_primary', 'primary_color'],
            'secondaryColor' => ['color_secondary', 'secondary_color'],
            'accentColor' => ['color_accent', 'accent_color'],
            'successColor' => ['color_success', 'success_color'],
            'dangerColor' => ['color_danger', 'danger_color'],
            'warningColor' => ['color_warning', 'warning_color'],
            'infoColor' => ['color_info', 'info_color'],
            'textPrimaryColor' => ['color_text_primary', 'text_primary_color'],
            'textSecondaryColor' => ['color_text_secondary', 'text_secondary_color'],
            'textMutedColor' => ['color_text_muted', 'text_muted_color'],
            'backgroundColor' => ['color_background', 'background_color'],
            'borderColor' => ['color_border', 'border_color'],
            'headerBgColor' => ['color_header_bg', 'header_bg_color'],
            'headerTextColor' => ['color_header_text', 'header_text_color'],
            'footerBgColor' => ['color_footer_bg', 'footer_bg_color'],
            'footerTextColor' => ['color_footer_text', 'footer_text_color'],
        ];
        
        // Property mapping ile eşleşme kontrolü
        foreach ($propertyMappings as $property => $alternatives) {
            if (in_array($name, $alternatives)) {
                $this->$property = $value;
                
                // Otomatik kontrast ayarı yapabilirsin (opsiyonel)
                if ($property === 'headerBgColor') {
                    // Header text rengini otomatik olarak ayarla
                    $this->headerTextColor = $this->generateContrastColor($value);
                }
                
                if ($property === 'footerBgColor') {
                    // Footer text rengini otomatik olarak ayarla
                    $this->footerTextColor = $this->generateContrastColor($value);
                }
                
                return;
            }
        }
    }
    
    /**
     * Debug metodu - renkleri konsola yazdırma
     */
    public function debugColors()
    {
        $colorProperties = [
            'primaryColor', 'secondaryColor', 'accentColor', 
            'successColor', 'dangerColor', 'warningColor', 'infoColor',
            'textPrimaryColor', 'textSecondaryColor', 'textMutedColor',
            'backgroundColor', 'borderColor', 
            'headerBgColor', 'headerTextColor', 'footerBgColor', 'footerTextColor'
        ];
        
        $colors = [];
        foreach ($colorProperties as $prop) {
            $colors[$prop] = $this->$prop;
        }
        
        // Renkleri JavaScript konsoluna gönder
        $this->dispatch('debug-colors', $colors);
    }
    
    /**
     * Rastgele renk paleti oluştur
     */
    public function generateRandomColors()
    {
        // Ana renkleri rastgele oluştur
        $this->primaryColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        $this->secondaryColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        $this->accentColor = '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        
        // Header ve footer renklerini ana renklerden türet
        $this->headerBgColor = $this->getDarkerShade($this->primaryColor, 0.3);
        $this->footerBgColor = $this->getDarkerShade($this->primaryColor, 0.5);
        
        // Text renklerini otomatik ayarla
        $this->headerTextColor = $this->generateContrastColor($this->headerBgColor);
        $this->footerTextColor = $this->generateContrastColor($this->footerBgColor);
    }
    
    /**
     * Rengin daha koyu veya açık tonunu üretme
     */
    protected function getDarkerShade($hexColor, $factor) 
    {
        // HEX değerini RGB'ye çevir
        $hex = ltrim($hexColor, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Rengi karartmak için değerleri azalt
        $r = max(0, $r * (1 - $factor));
        $g = max(0, $g * (1 - $factor));
        $b = max(0, $b * (1 - $factor));
        
        // RGB'yi HEX'e çevir
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
    
    public function render()
    {
        return view('theme-manager::livewire.color-preview', [
            'colorThemes' => $this->getColorThemes(),
        ]);
    }
}