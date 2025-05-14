<?php

namespace App\Plugins\Core\src\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasTenant
{
    /**
     * Modelin tenant (kiracı) kapsamı
     */
    protected static function bootHasTenant()
    {
        // Tenant izolasyonu etkin değilse işlemi atla
        if (!config('core.tenant_isolation', true)) {
            return;
        }

        // Kayıt oluşturulurken otomatik tenant_id ata
        static::creating(function ($model) {
            if (!$model->isDirty('tenant_id') && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });

        // Global scope ekle
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Super admin ise tenant filtreleme yapma
            if (auth()->check() && auth()->user()->hasRole('super-admin')) {
                return;
            }
            
            // Kullanıcı tenant'a sahipse tenant_id ile filtrele
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });
    }

    /**
     * Belirli bir tenant için kapsam
     */
    public function scopeForTenant(Builder $query, $tenantId = null)
    {
        return $query->where('tenant_id', $tenantId ?: auth()->user()?->tenant_id);
    }

    /**
     * Global tenant scope'u kaldır
     */
    public function scopeWithoutTenantScope(Builder $query)
    {
        return $query->withoutGlobalScope('tenant');
    }
}