<?php

namespace App\Plugins\UserManagement;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class UserManagementPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'user-management';
    }

    public function register(Panel $panel): void
    {
        // UserManagement plugin resource ve sayfalarını kaydet
        $panel
            ->resources([
                \App\Plugins\UserManagement\Filament\Resources\UserResource::class,
                \App\Plugins\UserManagement\Filament\Resources\Shield\RoleResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin başlatma işlemleri
    }
    
    public function isEnabled(): bool
    {
        return true;
    }
    
    public function getInfo(): array
    {
        return [
            'name' => 'UserManagement',
            'description' => 'Kullanıcı ve rol yönetimi için plugin',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}