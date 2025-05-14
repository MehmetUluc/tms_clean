<?php

namespace App\Plugins\Partner\Filament\Pages;

use Filament\Pages\Page;

class TestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Partner';
    
    protected static ?string $navigationLabel = 'Test Page';
    
    protected static ?string $title = 'Partner Test Page';
    
    protected static ?int $navigationSort = 100;
    
    protected static string $view = 'partner::filament.pages.test-page';
    
    public function mount(): void
    {
        // Any initialization code can go here
    }
}