<?php

namespace App\Plugins\Partner\Filament\Pages;

use Filament\Pages\Page;

class SimplePartnerDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Partner Dashboard';
    protected static string $view = 'filament.pages.partner-dashboard';
    
    public function mount(): void
    {
        // Basit dashboard
    }
}