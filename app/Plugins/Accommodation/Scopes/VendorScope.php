<?php

namespace App\Plugins\Accommodation\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Plugins\Vendor\Services\VendorService;

class VendorScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // Eğer kullanıcı giriş yapmamışsa kısıtlama uygulamıyoruz
        if (!auth()->check()) {
            return;
        }

        // Admin veya super_admin tüm otelleri görebilmeli
        if (auth()->user()->hasRole(['admin', 'super_admin'])) {
            return;
        }

        // Eğer vendor rolü varsa sadece kendi otellerini görsün
        if (auth()->user()->hasRole('vendor')) {
            $vendorService = app(VendorService::class);
            $vendor = $vendorService->getVendorForUser(auth()->user());
            
            if ($vendor) {
                $builder->where('vendor_id', $vendor->id);
            } else {
                // Eğer vendor bulunmazsa hiçbir şey gösterme
                $builder->where('id', 0); // Hiçbir sonuç dönmeyecek
            }
        }
    }
}