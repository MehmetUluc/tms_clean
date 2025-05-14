<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Plugins\Pricing\PricingPlugin;
use App\Plugins\Core\CorePlugin;
use App\Plugins\Hotel\HotelPlugin;
use App\Plugins\Accommodation\AccommodationPlugin;
use App\Plugins\Booking\BookingPlugin;
use App\Plugins\Amenities\AmenitiesPlugin;
use App\Plugins\Integration\IntegrationPlugin;
use App\Plugins\API\ApiPlugin;
use App\Plugins\UserManagement\UserManagementPlugin;
use App\Plugins\ThemeManager\ThemeManagerPlugin;
use App\Plugins\OTA\OTAPlugin;
use App\Plugins\MenuManager\MenuManagerPlugin;
use App\Plugins\Discount\DiscountPlugin;
// PricingV2 plugin artık kullanılmıyor

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->loginRouteSlug('login')
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Inter')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                CorePlugin::make(),
                PricingPlugin::make(),
                HotelPlugin::make(),
                AccommodationPlugin::make(),
                BookingPlugin::make(),
                AmenitiesPlugin::make(),
                ApiPlugin::make(),
                IntegrationPlugin::make(),
                UserManagementPlugin::make(),
                ThemeManagerPlugin::make(),
                OTAPlugin::make(), // OTA Plugin'i ekledik
                MenuManagerPlugin::make(), // Menu Manager Plugin'i ekledik
                DiscountPlugin::make(), // Discount Plugin'i ekledik
                \App\Plugins\Partner\PartnerPlugin::make(), // Partner Plugin'i ekledik (Vendor Plugin'in yerini aldı)
                // PricingV2Plugin kaldırıldı
                \Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin::make(),
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
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
            ])
;
    }
}