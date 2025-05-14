<?php

namespace App\Plugins\ThemeManager\View\Components;

use Illuminate\View\Component;
use App\Plugins\ThemeManager\Models\ThemeSetting;

class ThemeHead extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function render()
    {
        // Tema ayarlarını getir
        $seoSettings = ThemeSetting::where('group', 'seo')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
        
        $themeSettings = ThemeSetting::where('is_public', true)
            ->get()
            ->pluck('value', 'key')
            ->toArray();
        
        // View'e gönder
        return view('theme-manager::components.theme-head', [
            'seoSettings' => $seoSettings,
            'themeSettings' => $themeSettings
        ]);
    }
}