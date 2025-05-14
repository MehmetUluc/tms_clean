<?php

namespace App\Plugins\OTA;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class OTAPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'ota';
    }

    public function register(Panel $panel): void
    {
        // Register OTA plugin resources
        $panel
            ->resources([
                \App\Plugins\OTA\Filament\Resources\ChannelResource::class,
                \App\Plugins\OTA\Filament\Resources\XmlMappingResource::class,
            ])
            ->pages([
                \App\Plugins\OTA\Filament\Pages\XmlMappingWizard::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin bootstrapping logic
    }
    
    public function isEnabled(): bool
    {
        return true;
    }
    
    public function getInfo(): array
    {
        return [
            'name' => 'OTA Integration',
            'description' => 'Integration module for Online Travel Agencies with support for XML and JSON formats',
            'version' => '2.0.0',
            'author' => 'Filament',
        ];
    }
    
    public function getName(): string
    {
        return 'OTA Integration';
    }
    
    public function getDescription(): string
    {
        return 'Comprehensive integration module for Online Travel Agencies with support for multiple formats and bidirectional data flow';
    }
    
    public function install(): void
    {
        // Installation logic if needed
    }
    
    public function uninstall(): void
    {
        // Uninstallation logic if needed
    }
    
    public function getServiceProvider(): string
    {
        return OTAServiceProvider::class;
    }
    
    public function getMenuSort(): int
    {
        return 50;
    }
    
    public function getMenuIcon(): string
    {
        return 'heroicon-o-globe-alt';
    }
}