@props([
    'upload' => null,
])

@php
    $uploadPath = $upload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $upload->getFilename() : $upload;
    $fileExtension = pathinfo($uploadPath, PATHINFO_EXTENSION);
    
    $icon = match ($fileExtension) {
        'csv' => 'heroicon-o-table-cells',
        'doc', 'docx' => 'heroicon-o-document-text',
        'gif', 'jpg', 'jpeg', 'png', 'svg', 'webp' => 'heroicon-o-photo',
        'mp3', 'wav' => 'heroicon-o-musical-note',
        'mp4', 'mov', 'avi' => 'heroicon-o-film',
        'pdf' => 'heroicon-o-document',
        'ppt', 'pptx' => 'heroicon-o-presentation-chart-bar',
        'txt' => 'heroicon-o-document',
        'xls', 'xlsx' => 'heroicon-o-table-cells',
        'zip', 'rar', 'tar', 'gz' => 'heroicon-o-archive-box',
        default => 'heroicon-o-document',
    };
@endphp

<x-dynamic-component :component="$icon" {{ $attributes }} />