<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Register before gate callback to allow super_admin and ID 1 users to bypass permission checks
        Gate::before(function ($user, $ability) {
            if ($user->id === 1 || $user->hasRole('super_admin')) {
                return true;
            }
        });
    }
}