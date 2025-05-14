<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeSelector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always use inertia - force the theme to be inertia
        $theme = 'inertia';
        
        // Store theme preference in session
        session(['theme' => $theme]);
        
        // Apply middleware only for non-API routes
        if (!$request->is('api/*') && !$request->ajax() && $request->route() && !$request->routeIs('inertia.*')) {
            // Log current route
            \Log::info('Current route: ' . ($request->route()->getName() ?? 'null'));
            
            // Map regular routes to inertia routes
            $routeMapping = [
                // Home route is now directly using Inertia controller
                'about' => 'inertia.about',
                'contact' => 'inertia.contact',
                'terms' => 'inertia.terms',
                'privacy' => 'inertia.privacy',
                'faq' => 'inertia.faq',
                'hotels.index' => 'inertia.hotels.index',
                'hotels.show' => 'inertia.hotels.show',
                'hotels.rooms' => 'inertia.hotels.rooms',
                'regions.index' => 'inertia.destinations.index',
                'regions.show' => 'inertia.destinations.show',
            ];
            
            // Get current route name
            $currentRoute = $request->route()->getName();
            
            // If we have a mapping for this route, redirect to the inertia version
            if (isset($routeMapping[$currentRoute])) {
                \Log::info('Redirecting from ' . $currentRoute . ' to ' . $routeMapping[$currentRoute]);
                return redirect()->route($routeMapping[$currentRoute], $request->route()->parameters());
            }
        }
        
        return $next($request);
    }
}