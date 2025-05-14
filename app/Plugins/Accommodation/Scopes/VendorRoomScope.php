<?php

namespace App\Plugins\Accommodation\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Plugins\Vendor\Services\VendorService;

class VendorRoomScope implements Scope
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

        // Admin veya super_admin tüm odaları görebilmeli
        if (auth()->user()->hasRole(['admin', 'super_admin'])) {
            return;
        }

        // Eğer vendor rolü varsa sadece kendi otellerine ait odaları görsün
        if (auth()->user()->hasRole('vendor')) {
            $vendorService = app(VendorService::class);
            $vendor = $vendorService->getVendorForUser(auth()->user());
            
            if ($vendor) {
                $builder->whereHas('hotel', function ($query) use ($vendor) {
                    $query->where('vendor_id', $vendor->id);
                });
            } else {
                // Eğer vendor bulunmazsa hiçbir şey gösterme
                $builder->where('id', 0); // Hiçbir sonuç dönmeyecek
            }
        }
    }
}