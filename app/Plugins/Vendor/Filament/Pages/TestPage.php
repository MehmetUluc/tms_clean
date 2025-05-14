<?php

namespace App\Plugins\Vendor\Filament\Pages;

use Filament\Pages\Page;

class TestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Vendor';
    
    protected static ?string $navigationLabel = 'Test Page';
    
    protected static ?string $title = 'Vendor Test Page';
    
    protected static ?int $navigationSort = 100;
    
    protected static string $view = 'vendor::filament.pages.test-page';
    
    public function mount(): void
    {
        // Any initialization code can go here
    }
}