<?php

namespace App\Plugins\ThemeManager;

use App\Plugins\Core\src\Contracts\PluginInterface;
use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\ThemeManager\Filament\Pages\ManageThemeSettings;

class ThemeManagerPlugin implements PluginInterface, Plugin
{
    public static function make(): static
    {
        return new static();
    }

    /**
     * Plugin kimliği
     *
     * @return string
     */
    public function getId(): string
    {
        return 'theme-manager';
    }

    /**
     * Plugin adı
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Theme Manager';
    }

    /**
     * Plugin açıklaması
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'B2C temasının görünümünü ve ayarlarını yönetmenizi sağlar.';
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
        return ThemeManagerServiceProvider::class;
    }

    /**
     * Filament paneline register et
     *
     * @param Panel $panel
     * @return void
     */
    public function register(Panel $panel): void
    {
        $panel->pages([
            ManageThemeSettings::class,
        ]);
    }

    /**
     * Filament'a bootstrap uygulaması
     *
     * @param Panel $panel
     * @return void
     */
    public function boot(Panel $panel): void
    {
        // Filament için özel bootstrap işlemleri
    }
    
    /**
     * Plugin'in menüde gösterilme sırası
     *
     * @return int
     */
    public function getMenuSort(): int
    {
        return 80; // Menüde alt taraflarda görünsün
    }
    
    /**
     * Plugin'in menü ikonu
     *
     * @return string
     */
    public function getMenuIcon(): string
    {
        return 'heroicon-o-paint-brush';
    }
}