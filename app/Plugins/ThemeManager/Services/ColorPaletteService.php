<?php

namespace App\Plugins\ThemeManager\Services;

class ColorPaletteService
{
    /**
     * Tema renk paletlerini döndürür
     * 
     * @return array
     */
    public static function getPalettes(): array
    {
        return [
            'modern' => [
                'name' => 'Modern Mavi',
                'description' => 'Modern ve profesyonel görünüm için mavi tonlarında bir palet',
                'colors' => [
                    'primary' => '#3b82f6',    // Mavi
                    'secondary' => '#10b981',  // Yeşil
                    'accent' => '#8b5cf6',     // Mor
                    'warning' => '#f59e0b',    // Turuncu
                    'danger' => '#ef4444',     // Kırmızı
                    'success' => '#10b981',    // Yeşil
                    'info' => '#3b82f6',       // Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#1f2937',     // Koyu Gri
                    'text_secondary' => '#4b5563',   // Gri
                    'text_muted' => '#9ca3af',       // Açık Gri
                    'background' => '#ffffff',       // Beyaz
                    'border' => '#e5e7eb',           // Açık Gri
                    
                    // Status Light Colors
                    'success_light' => '#d1fae5',    // Açık Yeşil
                    'danger_light' => '#fee2e2',     // Açık Kırmızı
                    'warning_light' => '#fef3c7',    // Açık Sarı
                    
                    // Page Section Colors
                    'header_bg' => '#1e40af',        // Koyu Mavi
                    'header_text' => '#ffffff',      // Beyaz
                    'footer_bg' => '#1e293b',        // Koyu Lacivert
                    'footer_text' => '#f3f4f6',      // Açık Gri
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/modern.jpg',
            ],
            'luxury' => [
                'name' => 'Lüks Siyah & Altın',
                'description' => 'Lüks oteller için şık siyah ve altın tonlarında palet',
                'colors' => [
                    'primary' => '#f59e0b',    // Altın
                    'secondary' => '#1e293b',  // Koyu Lacivert
                    'accent' => '#c2410c',     // Turuncu
                    'warning' => '#eab308',    // Sarı
                    'danger' => '#b91c1c',     // Koyu Kırmızı
                    'success' => '#15803d',    // Koyu Yeşil
                    'info' => '#0369a1',       // Koyu Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#0f172a',     // Koyu Lacivert
                    'text_secondary' => '#334155',   // Koyu Gri
                    'text_muted' => '#64748b',       // Gri
                    'background' => '#ffffff',       // Beyaz
                    'border' => '#e2e8f0',           // Açık Gri
                    
                    // Status Light Colors
                    'success_light' => '#ecfccb',    // Açık Yeşil
                    'danger_light' => '#fee2e2',     // Açık Kırmızı
                    'warning_light' => '#fef9c3',    // Açık Sarı
                    
                    // Page Section Colors
                    'header_bg' => '#0f172a',        // Koyu Lacivert
                    'header_text' => '#f8fafc',      // Beyaz
                    'footer_bg' => '#0f172a',        // Koyu Lacivert
                    'footer_text' => '#e2e8f0',      // Açık Gri
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/luxury.jpg',
            ],
            'nature' => [
                'name' => 'Doğal Yeşil',
                'description' => 'Doğa dostu oteller için yeşil tonlarında sakin palet',
                'colors' => [
                    'primary' => '#15803d',    // Koyu Yeşil
                    'secondary' => '#4d7c0f',  // Yeşil
                    'accent' => '#0d9488',     // Turkuaz
                    'warning' => '#d97706',    // Turuncu
                    'danger' => '#dc2626',     // Kırmızı
                    'success' => '#16a34a',    // Yeşil
                    'info' => '#0284c7',       // Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#1f2937',     // Koyu Gri
                    'text_secondary' => '#4b5563',   // Gri
                    'text_muted' => '#9ca3af',       // Açık Gri
                    'background' => '#f8fafc',       // Açık Beyaz
                    'border' => '#e2e8f0',           // Açık Gri
                    
                    // Status Light Colors
                    'success_light' => '#dcfce7',    // Açık Yeşil
                    'danger_light' => '#fee2e2',     // Açık Kırmızı
                    'warning_light' => '#fef3c7',    // Açık Sarı
                    
                    // Page Section Colors
                    'header_bg' => '#14532d',        // Koyu Yeşil
                    'header_text' => '#f0fdf4',      // Açık Yeşil Beyaz
                    'footer_bg' => '#166534',        // Koyu Yeşil
                    'footer_text' => '#dcfce7',      // Açık Yeşil
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/nature.jpg',
            ],
            'ocean' => [
                'name' => 'Okyanus Mavisi',
                'description' => 'Sahil otellerine uygun mavi tonlarında ferah palet',
                'colors' => [
                    'primary' => '#0284c7',    // Mavi
                    'secondary' => '#0d9488',  // Turkuaz
                    'accent' => '#6366f1',     // İndigo
                    'warning' => '#fb923c',    // Turuncu
                    'danger' => '#f43f5e',     // Pembe
                    'success' => '#059669',    // Yeşil
                    'info' => '#38bdf8',       // Açık Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#0f172a',     // Koyu Lacivert
                    'text_secondary' => '#1e293b',   // Lacivert
                    'text_muted' => '#64748b',       // Gri
                    'background' => '#f8fafc',       // Açık Beyaz
                    'border' => '#e0f2fe',           // Açık Mavi
                    
                    // Status Light Colors
                    'success_light' => '#d1fae5',    // Açık Yeşil
                    'danger_light' => '#fee2e2',     // Açık Kırmızı
                    'warning_light' => '#fef3c7',    // Açık Sarı
                    
                    // Page Section Colors
                    'header_bg' => '#0c4a6e',        // Koyu Mavi
                    'header_text' => '#f0f9ff',      // Açık Mavi Beyaz
                    'footer_bg' => '#075985',        // Koyu Mavi
                    'footer_text' => '#e0f2fe',      // Açık Mavi
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/ocean.jpg',
            ],
            'sunset' => [
                'name' => 'Günbatımı',
                'description' => 'Sıcak ve canlı günbatımı tonlarında palet',
                'colors' => [
                    'primary' => '#f97316',    // Turuncu
                    'secondary' => '#be123c',  // Bordo
                    'accent' => '#c026d3',     // Mor
                    'warning' => '#fbbf24',    // Sarı
                    'danger' => '#e11d48',     // Kırmızı
                    'success' => '#65a30d',    // Yeşil
                    'info' => '#3b82f6',       // Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#27272a',     // Koyu Gri
                    'text_secondary' => '#52525b',   // Gri
                    'text_muted' => '#a1a1aa',       // Açık Gri
                    'background' => '#fffbeb',       // Açık Krem
                    'border' => '#fef3c7',           // Açık Sarı
                    
                    // Status Light Colors
                    'success_light' => '#d9f99d',    // Açık Yeşil
                    'danger_light' => '#fecdd3',     // Açık Pembe
                    'warning_light' => '#fed7aa',    // Açık Turuncu
                    
                    // Page Section Colors
                    'header_bg' => '#9a3412',        // Koyu Turuncu
                    'header_text' => '#fff7ed',      // Açık Krem
                    'footer_bg' => '#7c2d12',        // Koyu Turuncu/Kahve
                    'footer_text' => '#ffedd5',      // Açık Turuncu/Krem
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/sunset.jpg',
            ],
            'monochrome' => [
                'name' => 'Monokrom Şık',
                'description' => 'Minimalist tasarımlar için gri tonlarında şık palet',
                'colors' => [
                    'primary' => '#0f172a',    // Koyu Lacivert
                    'secondary' => '#334155',  // Koyu Gri
                    'accent' => '#64748b',     // Gri
                    'warning' => '#f59e0b',    // Turuncu
                    'danger' => '#ef4444',     // Kırmızı
                    'success' => '#10b981',    // Yeşil
                    'info' => '#0ea5e9',       // Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#1f2937',     // Koyu Gri
                    'text_secondary' => '#374151',   // Gri
                    'text_muted' => '#9ca3af',       // Açık Gri
                    'background' => '#f9fafb',       // Açık Gri-Beyaz
                    'border' => '#e5e7eb',           // Açık Gri
                    
                    // Status Light Colors
                    'success_light' => '#d1fae5',    // Açık Yeşil
                    'danger_light' => '#fee2e2',     // Açık Kırmızı
                    'warning_light' => '#fef3c7',    // Açık Sarı
                    
                    // Page Section Colors
                    'header_bg' => '#1f2937',        // Koyu Gri
                    'header_text' => '#f9fafb',      // Açık Gri-Beyaz
                    'footer_bg' => '#111827',        // Koyu Gri-Siyah
                    'footer_text' => '#e5e7eb',      // Açık Gri
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/monochrome.jpg',
            ],
            'purple' => [
                'name' => 'Mor Esintisi',
                'description' => 'Sıradışı ve yaratıcı tasarımlar için mor ağırlıklı palet',
                'colors' => [
                    'primary' => '#8b5cf6',    // Mor
                    'secondary' => '#d946ef',  // Pembe
                    'accent' => '#6366f1',     // İndigo
                    'warning' => '#fbbf24',    // Sarı
                    'danger' => '#ef4444',     // Kırmızı
                    'success' => '#10b981',    // Yeşil
                    'info' => '#3b82f6',       // Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#1e1b4b',     // Koyu İndigo
                    'text_secondary' => '#4338ca',   // İndigo
                    'text_muted' => '#a5b4fc',       // Açık İndigo
                    'background' => '#f5f3ff',       // Açık Mor
                    'border' => '#e9d5ff',           // Açık Mor
                    
                    // Status Light Colors
                    'success_light' => '#d1fae5',    // Açık Yeşil
                    'danger_light' => '#fee2e2',     // Açık Kırmızı
                    'warning_light' => '#fef3c7',    // Açık Sarı
                    
                    // Page Section Colors
                    'header_bg' => '#5b21b6',        // Koyu Mor
                    'header_text' => '#f5f3ff',      // Açık Mor
                    'footer_bg' => '#4c1d95',        // Koyu Mor
                    'footer_text' => '#e9d5ff',      // Açık Mor
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/purple.jpg',
            ],
            'earthy' => [
                'name' => 'Toprak Tonları',
                'description' => 'Doğal ve sıcak toprak tonlarında klasik palet',
                'colors' => [
                    'primary' => '#b45309',    // Kahverengi
                    'secondary' => '#a16207',  // Altın Sarısı
                    'accent' => '#78350f',     // Koyu Kahve
                    'warning' => '#fbbf24',    // Sarı
                    'danger' => '#b91c1c',     // Koyu Kırmızı
                    'success' => '#15803d',    // Koyu Yeşil
                    'info' => '#1e40af',       // Koyu Mavi
                    
                    // Text & Background Colors
                    'text_primary' => '#422006',     // Çok Koyu Kahve
                    'text_secondary' => '#713f12',   // Koyu Kahve
                    'text_muted' => '#a16207',       // Orta Kahve
                    'background' => '#fffbeb',       // Açık Krem
                    'border' => '#fef3c7',           // Açık Sarı
                    
                    // Status Light Colors
                    'success_light' => '#ecfccb',    // Açık Yeşil
                    'danger_light' => '#fee2e2',     // Açık Kırmızı
                    'warning_light' => '#fef9c3',    // Açık Sarı
                    
                    // Page Section Colors
                    'header_bg' => '#78350f',        // Koyu Kahve
                    'header_text' => '#fef3c7',      // Açık Sarı
                    'footer_bg' => '#451a03',        // Çok Koyu Kahve
                    'footer_text' => '#fef3c7',      // Açık Sarı
                ],
                'preview_url' => '/vendor/theme-manager/images/palettes/earthy.jpg',
            ],
        ];
    }
    
    /**
     * Belirli bir paletin renk değerlerini döndürür
     * 
     * @param string $paletteKey
     * @return array|null
     */
    public static function getPalette(string $paletteKey): ?array
    {
        $palettes = self::getPalettes();
        
        return $palettes[$paletteKey]['colors'] ?? null;
    }
    
    /**
     * Paletin seçim için metnini oluşturur
     * 
     * @param string $paletteKey
     * @param array $paletteData
     * @return string
     */
    public static function renderPaletteOption(string $paletteKey, array $paletteData): string
    {
        $colorNames = [
            'primary' => 'Ana',
            'secondary' => 'İkincil', 
            'accent' => 'Vurgu', 
            'success' => 'Başarı', 
            'warning' => 'Uyarı', 
            'danger' => 'Tehlike'
        ];
        
        $colors = $paletteData['colors'];
        
        // Daha basit bir açıklama metni oluştur
        $text = $paletteData['name'] . " - " . $paletteData['description'];
        
        return $text;
    }
    
    /**
     * Tüm paletleri seçim için listeler
     * 
     * @return array
     */
    public static function getPalettesForSelect(): array
    {
        $palettes = self::getPalettes();
        $options = [];
        
        foreach ($palettes as $key => $palette) {
            $options[$key] = self::renderPaletteOption($key, $palette);
        }
        
        return $options;
    }
    
    /**
     * Varsayılan renk paleti anahtarını döndürür
     * 
     * @return string
     */
    public static function getDefaultPaletteKey(): string
    {
        return 'modern';
    }
}