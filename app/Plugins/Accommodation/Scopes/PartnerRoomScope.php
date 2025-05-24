<?php

namespace App\Plugins\Accommodation\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Plugins\Partner\Services\PartnerService;

class PartnerRoomScope implements Scope
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

        // Eğer partner rolü varsa sadece kendi otellerine ait odaları görsün
        if (auth()->user()->hasRole('partner')) {
            $partnerService = app(PartnerService::class);
            $partner = $partnerService->getPartnerForUser(auth()->user());
            
            if ($partner) {
                $builder->whereHas('hotel', function ($query) use ($partner) {
                    $query->where('partner_id', $partner->id);
                });
            } else {
                // Eğer partner bulunmazsa hiçbir şey gösterme
                $builder->where('id', 0); // Hiçbir sonuç dönmeyecek
            }
        }
        
        // Eğer partner_staff rolü varsa partner'ın otellerine ait odaları görsün
        if (auth()->user()->hasRole('partner_staff')) {
            $partner = auth()->user()->partner;
            
            if ($partner) {
                $builder->whereHas('hotel', function ($query) use ($partner) {
                    $query->where('partner_id', $partner->id);
                });
            } else {
                // Eğer partner bulunmazsa hiçbir şey gösterme
                $builder->where('id', 0); // Hiçbir sonuç dönmeyecek
            }
        }
    }
}