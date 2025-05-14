<?php

namespace App\Plugins\Partner\Filament\Pages;

use Filament\Pages\Page;

class StaticTestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Partner';
    
    protected static ?string $navigationLabel = 'Static Test Page';
    
    protected static ?string $title = 'Static Test Page';
    
    protected static ?int $navigationSort = 102;
    
    // Use partner namespace to reference the view
    protected static string $view = 'partner::filament.pages.static-test-page';
}