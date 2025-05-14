<?php

namespace App\Plugins\ThemeManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Plugins\ThemeManager\Models\ThemeSetting;

class ThemeController extends Controller
{
    /**
     * Tema değişkenlerini içeren CSS dosyasını dinamik olarak üretir
     * 
     * @return Response
     */
    public function generateCss(): Response
    {
        // Tüm renk ayarlarını al
        $colors = ThemeSetting::where('group', 'colors')
            ->orWhere('key', 'like', '%color%')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
            
        // Typography ayarlarını al
        $typography = ThemeSetting::where('group', 'typography')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
        
        // CSS değişkenlerini oluştur
        $css = ":root {\n";
        
        // Renk değişkenlerini ekle
        foreach ($colors as $key => $value) {
            $variableName = str_replace('color_', '', $key);
            $variableName = str_replace('_', '-', $variableName);
            $css .= "  --theme-{$variableName}: {$value};\n";
        }
        
        // Typography değişkenlerini ekle
        foreach ($typography as $key => $value) {
            $variableName = str_replace('typography_', '', $key);
            $variableName = str_replace('_', '-', $variableName);
            $css .= "  --theme-{$variableName}: {$value};\n";
        }
        
        $css .= "}\n\n";
        
        // Utility sınıfları ekle
        $css .= "/* Utility classes */\n";
        
        // Text colors
        $css .= "/* Text Colors */\n";
        $css .= ".text-primary { color: var(--theme-primary) !important; }\n";
        $css .= ".text-secondary { color: var(--theme-secondary) !important; }\n";
        $css .= ".text-accent { color: var(--theme-accent) !important; }\n";
        $css .= ".text-success { color: var(--theme-success) !important; }\n";
        $css .= ".text-warning { color: var(--theme-warning) !important; }\n";
        $css .= ".text-danger { color: var(--theme-danger) !important; }\n";
        $css .= ".text-info { color: var(--theme-info) !important; }\n";
        $css .= ".text-body { color: var(--theme-text-primary) !important; }\n";
        $css .= ".text-body-secondary { color: var(--theme-text-secondary) !important; }\n";
        $css .= ".text-muted { color: var(--theme-text-muted) !important; }\n\n";
        
        // Background colors
        $css .= "/* Background Colors */\n";
        $css .= ".bg-primary { background-color: var(--theme-primary) !important; }\n";
        $css .= ".bg-secondary { background-color: var(--theme-secondary) !important; }\n";
        $css .= ".bg-accent { background-color: var(--theme-accent) !important; }\n";
        $css .= ".bg-success { background-color: var(--theme-success) !important; }\n";
        $css .= ".bg-warning { background-color: var(--theme-warning) !important; }\n";
        $css .= ".bg-danger { background-color: var(--theme-danger) !important; }\n";
        $css .= ".bg-info { background-color: var(--theme-info) !important; }\n";
        $css .= ".bg-light { background-color: var(--theme-background) !important; }\n";
        $css .= ".bg-body { background-color: var(--theme-background) !important; }\n";
        $css .= ".bg-header { background-color: var(--theme-header-bg) !important; }\n";
        $css .= ".bg-footer { background-color: var(--theme-footer-bg) !important; }\n";
        $css .= ".bg-success-light { background-color: var(--theme-success-light) !important; }\n";
        $css .= ".bg-danger-light { background-color: var(--theme-danger-light) !important; }\n";
        $css .= ".bg-warning-light { background-color: var(--theme-warning-light) !important; }\n\n";
        
        // Border colors
        $css .= "/* Border Colors */\n";
        $css .= ".border-primary { border-color: var(--theme-primary) !important; }\n";
        $css .= ".border-secondary { border-color: var(--theme-secondary) !important; }\n";
        $css .= ".border-accent { border-color: var(--theme-accent) !important; }\n";
        $css .= ".border-success { border-color: var(--theme-success) !important; }\n";
        $css .= ".border-warning { border-color: var(--theme-warning) !important; }\n";
        $css .= ".border-danger { border-color: var(--theme-danger) !important; }\n";
        $css .= ".border-info { border-color: var(--theme-info) !important; }\n";
        $css .= ".border-light { border-color: var(--theme-border) !important; }\n\n";
        
        // Page section styles
        $css .= "/* Page Section Styles */\n";
        $css .= "header, .header { background-color: var(--theme-header-bg) !important; color: var(--theme-header-text) !important; }\n";
        $css .= "footer, .footer { background-color: var(--theme-footer-bg) !important; color: var(--theme-footer-text) !important; }\n";
        $css .= "body { background-color: var(--theme-background) !important; color: var(--theme-text-primary) !important; }\n\n";
        
        // Typography sınıfları
        if (isset($typography['typography_heading_font'])) {
            $css .= "h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {\n";
            $css .= "  font-family: var(--theme-heading-font);\n";
            $css .= "}\n\n";
        }
        
        if (isset($typography['typography_body_font'])) {
            $css .= "body, p, .body-text {\n";
            $css .= "  font-family: var(--theme-body-font);\n";
            $css .= "}\n\n";
        }
        
        // Button stillerini ekle
        $css .= ".btn-primary {\n";
        $css .= "  background-color: var(--theme-primary);\n";
        $css .= "  border-color: var(--theme-primary);\n";
        $css .= "  color: white;\n";
        $css .= "}\n\n";
        
        $css .= ".btn-primary:hover {\n";
        $css .= "  background-color: color-mix(in srgb, var(--theme-primary) 80%, black);\n";
        $css .= "  border-color: color-mix(in srgb, var(--theme-primary) 80%, black);\n";
        $css .= "}\n\n";
        
        $css .= ".btn-outline-primary {\n";
        $css .= "  border-color: var(--theme-primary);\n";
        $css .= "  color: var(--theme-primary);\n";
        $css .= "}\n\n";
        
        $css .= ".btn-outline-primary:hover {\n";
        $css .= "  background-color: var(--theme-primary);\n";
        $css .= "  color: white;\n";
        $css .= "}\n\n";
        
        // Ek stilleri ekle
        $css .= "/* Theme additional styles */\n";
        $css .= "a { color: var(--theme-primary); }\n";
        $css .= "a:hover { color: color-mix(in srgb, var(--theme-primary) 80%, black); }\n";
        
        // Response olarak CSS döndür
        return response($css, 200)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=86400'); // 1 gün cache
    }
    
    /**
     * Tema ayarlarını JSON olarak API'da paylaş
     *
     * @return Response
     */
    public function getThemeSettings(): Response
    {
        $settings = ThemeSetting::where('is_public', true)
            ->get(['key', 'value', 'type'])
            ->keyBy('key')
            ->map(function ($item) {
                // Veri tipine göre casting yap
                switch ($item->type) {
                    case 'integer':
                    case 'number':
                        return (int) $item->value;
                    case 'float':
                        return (float) $item->value;
                    case 'boolean':
                        return (bool) $item->value;
                    case 'json':
                        return json_decode($item->value, true);
                    default:
                        return $item->value;
                }
            })
            ->toArray();
            
        return response()->json([
            'data' => $settings,
        ]);
    }
    
    /**
     * Tema ayarlarını grup grup JSON olarak API'da paylaş
     *
     * @return Response
     */
    public function getThemeSettingsByGroup(): Response
    {
        $settings = ThemeSetting::where('is_public', true)
            ->get(['key', 'value', 'type', 'group'])
            ->groupBy('group')
            ->map(function ($group) {
                return $group->keyBy('key')->map(function ($item) {
                    // Veri tipine göre casting yap
                    switch ($item->type) {
                        case 'integer':
                        case 'number':
                            return (int) $item->value;
                        case 'float':
                            return (float) $item->value;
                        case 'boolean':
                            return (bool) $item->value;
                        case 'json':
                            return json_decode($item->value, true);
                        default:
                            return $item->value;
                    }
                });
            })
            ->toArray();
            
        return response()->json([
            'data' => $settings,
        ]);
    }
}