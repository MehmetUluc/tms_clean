<?php

namespace App\Plugins\Core\src\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

abstract class BaseResource extends Resource
{
    /**
     * Modelin tenant scope'unu kullanıp kullanmayacağı
     */
    protected static bool $useTenantScope = true;
    
    /**
     * Global olarak silinenleri gösterme filtresini aktifleştirin
     */
    protected static bool $globallyShowTrashed = false;
    
    /**
     * Tenant scope'u kullanarak veriyi filtrele
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        // Silinenleri göster seçeneği
        if (static::$globallyShowTrashed && static::getModel()::usesSoftDeletes()) {
            $query->withTrashed();
        }
        
        // Tenant scope'u kullan
        if (static::$useTenantScope && method_exists(static::getModel(), 'bootHasTenant')) {
            $query->forTenant();
        }
        
        return $query;
    }
    
    /**
     * Resource'un silinmiş kayıtları gösterebilmesi için model hazırlığı
     */
    public static function getModel(): string
    {
        return parent::getModel();
    }
    
    /**
     * Tenant scope kullanımını ayarla
     */
    public static function useTenantScope(bool $use = true): void
    {
        static::$useTenantScope = $use;
    }
    
    /**
     * Silinmiş kayıtları gösterme ayarı
     */
    public static function showTrashed(bool $show = true): void
    {
        static::$globallyShowTrashed = $show;
    }
}