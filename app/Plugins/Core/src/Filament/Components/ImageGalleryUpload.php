<?php

namespace App\Plugins\Core\src\Filament\Components;

use Filament\Forms\Components\FileUpload;
use Closure;

class ImageGalleryUpload extends FileUpload
{
    // Using standard FileUpload instead of custom view
    
    protected bool $showAsGrid = true;
    protected int $gridColumns = 4;
    protected int $thumbnailSize = 150;
    
    public function showAsGrid(bool $condition = true): static
    {
        $this->showAsGrid = $condition;
        
        return $this;
    }
    
    public function gridColumns(int $columns): static
    {
        $this->gridColumns = $columns;
        
        return $this;
    }
    
    public function thumbnailSize(int $size): static
    {
        $this->thumbnailSize = $size;
        
        return $this;
    }
    
    public function getShowAsGrid(): bool
    {
        return $this->showAsGrid;
    }
    
    public function getGridColumns(): int
    {
        return $this->gridColumns;
    }
    
    public function getThumbnailSize(): int
    {
        return $this->thumbnailSize;
    }
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Varsayılan form ayarlarını uygula
        $this->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
             ->directory('uploads')
             ->disk('public')
             ->visibility('public')
             ->multiple()
             ->panelAspectRatio('16:9')
             ->panelLayout('integrated');
            
        // Yapılandırma ayarlarını uygula
        if (config('core.form_components.file_upload.show_as_grid')) {
            $this->showAsGrid(config('core.form_components.file_upload.show_as_grid'));
        }
        
        if (config('core.form_components.file_upload.grid_columns')) {
            $this->gridColumns(config('core.form_components.file_upload.grid_columns'));
        }
        
        if (config('core.form_components.file_upload.thumbnail_size')) {
            $this->thumbnailSize(config('core.form_components.file_upload.thumbnail_size'));
        }
    }
}