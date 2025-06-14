<?php

namespace App\Plugins\Vendor\Filament\Pages;

use Filament\Pages\Page;

class SimpleTestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Vendor';
    
    protected static ?string $navigationLabel = 'Simple Test';
    
    protected static ?string $title = 'Simple Test Page';
    
    protected static ?int $navigationSort = 101;
    
    // No view property - let it use default convention
}