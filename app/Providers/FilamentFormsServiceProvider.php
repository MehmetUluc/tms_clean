<?php

namespace App\Providers;

use Filament\Forms\FormsServiceProvider;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class FilamentFormsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->booting(function () {
            // Bu, forms görünümlerini forms:: ön ekiyle kaydeder
            $this->loadViewsFrom(resource_path('views/vendor/forms'), 'forms');
        });
    }

    public function boot(): void
    {
        // forms:: ön ekiyle kaydedilen görünümlerin yolunu belirt
        Blade::componentNamespace('App\\View\\Components\\Forms', 'forms');
        
        // form bileşenlerini mevcut blade ön ekiyle de kullanılabilir yap
        Blade::component('forms::partials.file-upload.files-list', 'filament-forms::partials.file-upload.files-list');
        Blade::component('forms::partials.file-upload.file-icon', 'filament-forms::partials.file-upload.file-icon');
        Blade::component('forms::partials.file-upload.actions.delete-file', 'filament-forms::partials.file-upload.actions.delete-file');
    }
}