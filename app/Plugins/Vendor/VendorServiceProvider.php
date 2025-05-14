<?php

namespace App\Plugins\Vendor;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use App\Plugins\Vendor\Services\VendorService;

class VendorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the vendor service
        $this->app->singleton(VendorService::class);

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/config/vendor.php', 'vendor'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

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
            __DIR__ . '/config/vendor.php' => config_path('vendor.php')
        ], 'vendor-config');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'vendor');
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
            // Define Vendor-specific permissions
            $permissions = [
                // Vendor management
                'vendor_view_any',
                'vendor_view',
                'vendor_create',
                'vendor_update',
                'vendor_delete',
                'vendor_activate',
                'vendor_deactivate',
                'vendor_manage_commissions',

                // Hotel management (Vendor specific)
                'vendor_view_hotels',
                'vendor_create_hotel',
                'vendor_update_hotel',
                'vendor_delete_hotel',
                'vendor_manage_hotel_inventory',
                'vendor_manage_hotel_pricing',

                // Room management
                'vendor_view_rooms',
                'vendor_create_room',
                'vendor_update_room',
                'vendor_delete_room',

                // Reservation management
                'vendor_view_reservations',
                'vendor_manage_reservations',

                // Financial management
                'vendor_view_financial',
                'vendor_request_payment',
                'vendor_view_transactions',
                'vendor_manage_bank_accounts',

                // Document management
                'vendor_view_documents',
                'vendor_upload_document',
                'vendor_delete_document',

                // Ministry reporting
                'vendor_view_ministry_reports',
                'vendor_create_ministry_report',
                'vendor_submit_ministry_report',
                'vendor_delete_ministry_report',
            ];

            // Create permissions if they don't exist
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            // Create vendor role if it doesn't exist
            $vendorRole = Role::firstOrCreate(['name' => 'vendor']);
            
            // Assign vendor-specific permissions to vendor role
            $vendorPermissions = [
                'vendor_view_hotels',
                'vendor_update_hotel',
                'vendor_manage_hotel_inventory',
                'vendor_manage_hotel_pricing',
                'vendor_view_rooms',
                'vendor_create_room',
                'vendor_update_room',
                'vendor_delete_room',
                'vendor_view_reservations',
                'vendor_view_financial',
                'vendor_request_payment',
                'vendor_view_transactions',
                'vendor_manage_bank_accounts',
                'vendor_view_documents',
                'vendor_upload_document',
                'vendor_view_ministry_reports',
                'vendor_create_ministry_report',
                'vendor_submit_ministry_report',
            ];

            foreach ($vendorPermissions as $permission) {
                $vendorRole->givePermissionTo($permission);
            }
        }
    }

    /**
     * Register blade components.
     */
    protected function registerBladeComponents(): void
    {
        Blade::directive('vendor', function () {
            return "<?php if(auth()->check() && auth()->user()->hasRole('vendor')): ?>";
        });

        Blade::directive('endvendor', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('notvendor', function () {
            return "<?php if(auth()->check() && !auth()->user()->hasRole('vendor')): ?>";
        });

        Blade::directive('endnotvendor', function () {
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