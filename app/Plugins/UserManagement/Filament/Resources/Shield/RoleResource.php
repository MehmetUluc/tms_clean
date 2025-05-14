<?php

namespace App\Plugins\UserManagement\Filament\Resources\Shield;

use BezhanSalleh\FilamentShield\Resources\RoleResource as ShieldRoleResource;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;

class RoleResource extends ShieldRoleResource
{
    protected static ?string $navigationGroup = 'Sistem';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Roller';
    }

    public static function getModelLabel(): string
    {
        return 'Rol';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Roller';
    }
}