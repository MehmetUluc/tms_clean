<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPartner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/partner/login');
        }
        
        // Super admin should not access partner panel
        if (auth()->user()->isSuperAdmin()) {
            return redirect('/admin');
        }
        
        // Check if user has partner role
        if (!auth()->user()->hasRole('partner')) {
            abort(403, 'Access denied. Partner role required.');
        }
        
        // Check if partner has completed onboarding
        $partner = auth()->user()->getAssociatedPartner();
        
        // Allow access to onboarding route
        if ($request->is('partner/partner-onboarding') || $request->routeIs('filament.partner.pages.partner-onboarding')) {
            return $next($request);
        }
        
        // If partner hasn't completed onboarding, redirect to onboarding
        if ($partner && !$partner->onboarding_completed) {
            return redirect('/partner/partner-onboarding');
        }
        
        return $next($request);
    }
}
