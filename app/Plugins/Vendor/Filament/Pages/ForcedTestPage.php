<?php

namespace App\Plugins\Vendor\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;

class ForcedTestPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Vendor';
    
    protected static ?string $navigationLabel = 'Forced Test';
    
    protected static ?string $title = 'Forced Test Page';
    
    protected static ?int $navigationSort = 105;
    
    // No view property - we'll override the render method
    
    public function mount(): void
    {
        // No need to create the view file, it's already been created
    }

    // Add the view property
    protected static string $view = 'vendor::filament.pages.forced-test-page';
}