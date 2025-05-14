<?php

namespace App\Plugins\Core\src\Contracts;

use Filament\Panel;

interface PluginInterface
{
    /**
     * Plugin nesnesini oluştur
     */
    public static function make(): static;
    
    /**
     * Plugin kimliğini döndür
     */
    public function getId(): string;
    
    /**
     * Plugin'i panele kaydet
     */
    public function register(Panel $panel): void;
    
    /**
     * Plugin'i başlat
     */
    public function boot(Panel $panel): void;
    
    /**
     * Plugin durumunu döndür (aktif/pasif)
     */
    public function isEnabled(): bool;
    
    /**
     * Plugin bilgilerini döndür
     */
    public function getInfo(): array;
}