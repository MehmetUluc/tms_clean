<?php

namespace App\Plugins\Partner;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use App\Plugins\Partner\Services\PartnerService;

class PartnerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the partner service
        $this->app->singleton(PartnerService::class);

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/config/partner.php', 'partner'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load migrations - always needed
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Skip everything else for non-partner routes
        $currentPath = request()->path();
        $isPartnerRoute = str_starts_with($currentPath, 'partner/') || 
                         str_starts_with($currentPath, 'admin/') ||
                         str_starts_with($currentPath, 'livewire/');
                         
        if (!$isPartnerRoute) {
            return; // Skip booting for B2C routes
        }

        // Register routes
        $this->registerRoutes();

        // Register policies
        $this->registerPolicies();

        // Register permissions and roles
        $this->registerPermissionsAndRoles();

        // Register commands
        // $this->commands([
        //     // Commands here
        // ]);

        // Publishes
        $this->publishes([
            __DIR__ . '/config/vendor.php' => config_path('partner.php')
        ], 'partner-config');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'partner');
        $this->loadViewsFrom(resource_path('views/filament'), 'filament');

        // Register Blade components
        $this->registerBladeComponents();

        // Register assets
        $this->registerAssets();
    }

    /**
     * Register routes.
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }

    /**
     * Register policies.
     */
    protected function registerPolicies(): void
    {
        // Add policies here as needed
    }

    /**
     * Register permissions and roles.
     */
    protected function registerPermissionsAndRoles(): void
    {
        // Only run in web requests to avoid running during migrations
        if (!$this->app->runningInConsole() && !$this->app->runningUnitTests()) {
            // Skip permission checks for frontend routes
            $currentRoute = request()->route();
            if ($currentRoute) {
                $routeName = $currentRoute->getName();
                $routePrefix = explode('.', $routeName ?? '')[0] ?? '';
                
                // Skip for non-admin/partner routes
                if (!in_array($routePrefix, ['filament', 'admin', 'partner'])) {
                    return;
                }
            }
            
            try {
            // Define Partner-specific permissions
            $permissions = [
                // Partner management
                'partner_view_any',
                'partner_view',
                'partner_create',
                'partner_update',
                'partner_delete',
                'partner_activate',
                'partner_deactivate',
                'partner_manage_commissions',

                // Hotel management (Partner specific)
                'partner_view_hotels',
                'partner_create_hotel',
                'partner_update_hotel',
                'partner_delete_hotel',
                'partner_manage_hotel_inventory',
                'partner_manage_hotel_pricing',

                // Room management
                'partner_view_rooms',
                'partner_create_room',
                'partner_update_room',
                'partner_delete_room',

                // Reservation management
                'partner_view_reservations',
                'partner_manage_reservations',

                // Financial management
                'partner_view_financial',
                'partner_request_payment',
                'partner_view_transactions',
                'partner_manage_bank_accounts',

                // Document management
                'partner_view_documents',
                'partner_upload_document',
                'partner_delete_document',

                // Ministry reporting
                'partner_view_ministry_reports',
                'partner_create_ministry_report',
                'partner_submit_ministry_report',
                'partner_delete_ministry_report',
            ];

            // Create permissions if they don't exist
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            // Create partner role if it doesn't exist
            $partnerRole = Role::firstOrCreate(['name' => 'partner']);

            // Assign partner-specific permissions to partner role
            $partnerPermissions = [
                'partner_view_hotels',
                'partner_update_hotel',
                'partner_manage_hotel_inventory',
                'partner_manage_hotel_pricing',
                'partner_view_rooms',
                'partner_create_room',
                'partner_update_room',
                'partner_delete_room',
                'partner_view_reservations',
                'partner_view_financial',
                'partner_request_payment',
                'partner_view_transactions',
                'partner_manage_bank_accounts',
                'partner_view_documents',
                'partner_upload_document',
                'partner_view_ministry_reports',
                'partner_create_ministry_report',
                'partner_submit_ministry_report',
            ];

            foreach ($partnerPermissions as $permission) {
                $partnerRole->givePermissionTo($permission);
            }
            } catch (\Exception $e) {
                // Log the error but don't break the application
                \Log::warning('Failed to register partner permissions: ' . $e->getMessage());
            }
        }
    }

    /**
     * Register blade components.
     */
    protected function registerBladeComponents(): void
    {
        Blade::directive('partner', function () {
            return "<?php if(auth()->check() && auth()->user()->hasRole('partner')): ?>";
        });

        Blade::directive('endpartner', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('notpartner', function () {
            return "<?php if(auth()->check() && !auth()->user()->hasRole('partner')): ?>";
        });

        Blade::directive('endnotpartner', function () {
            return "<?php endif; ?>";
        });
    }

    /**
     * Register assets.
     */
    protected function registerAssets(): void
    {
        // Register CSS and JS assets if needed
    }
}