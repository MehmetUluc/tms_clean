<?php

namespace App\Plugins\Vendor\Filament\Pages;

use Filament\Pages\Page;

class BasicTestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Vendor';
    
    protected static ?string $navigationLabel = 'Basic Test';
    
    protected static ?string $title = 'Basic Test Page';
    
    protected static ?int $navigationSort = 103;
    
    protected static string $view = 'vendor::filament.pages.basic-test-page';
}