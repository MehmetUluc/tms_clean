<?php

namespace App\Plugins\MenuManager;

use App\Plugins\Core\src\Contracts\PluginInterface;
use Filament\Contracts\Plugin;
use Filament\Panel;

class MenuManagerPlugin implements PluginInterface, Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'menu-manager';
    }

    /**
     * Plugin adı
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Menu Manager';
    }

    /**
     * Plugin açıklaması
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Site menülerini yönetmenizi sağlar.';
    }

    /**
     * Plugin bilgilerini al
     *
     * @return array
     */
    public function getInfo(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'version' => '1.0.0',
        ];
    }

    /**
     * Plugin aktif mi?
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * Plugin kurulumu
     */
    public function install(): void
    {
        // Bu plugin için özel kurulum işlemleri
    }

    /**
     * Pluginden ayrılırken yapılması gerekenler
     */
    public function uninstall(): void
    {
        // Bu plugin için kaldırma işlemleri
    }

    /**
     * Plugin'in servis sağlayıcısını al
     *
     * @return string
     */
    public function getServiceProvider(): string
    {
        return MenuManagerServiceProvider::class;
    }

    public function register(Panel $panel): void
    {
        // Register plugin resources
        $panel
            ->resources([
                \App\Plugins\MenuManager\Filament\Resources\MenuResource::class,
            ])
            ->pages([
                // Add pages here when created
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Boot logic here
    }
    
    /**
     * Plugin'in menüde gösterilme sırası
     *
     * @return int
     */
    public function getMenuSort(): int
    {
        return 70; // Menüde gösterilme sırası
    }
    
    /**
     * Plugin'in menü ikonu
     *
     * @return string
     */
    public function getMenuIcon(): string
    {
        return 'heroicon-o-bars-3';
    }
}