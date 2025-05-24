<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\EnsureUserIsPartner;
use App\Plugins\Partner\Filament\Resources\PartnerHotelResource;
use App\Plugins\Partner\Filament\Resources\PartnerRoomResource;
use App\Plugins\Partner\Filament\Resources\PartnerReservationResource;
use App\Plugins\Partner\Filament\Resources\PartnerStaffResource;

class PartnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('partner')
            ->path('partner')
            ->login()
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'danger' => Color::Red,
                'info' => Color::Sky,
            ])
            ->brandName('Turistik Partner Portal')
            ->brandLogo(asset('images/logo-light.svg'))
            ->darkModeBrandLogo(asset('images/logo-dark.svg'))
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                'Otel Yönetimi',
                'Rezervasyonlar',
                'Ayarlar',
            ])
            ->resources([
                PartnerHotelResource::class,
                PartnerRoomResource::class,
                PartnerReservationResource::class,
                PartnerStaffResource::class,
            ])
            ->discoverPages(in: app_path('Plugins/Partner/Filament/Pages'), for: 'App\\Plugins\\Partner\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Plugins/Partner/Filament/Widgets'), for: 'App\\Plugins\\Partner\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsPartner::class,
            ])
            ->plugins([
                \App\Plugins\Partner\PartnerPlugin::make(),
            ])
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->spa();
    }
}