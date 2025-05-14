<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::channel('single')->info('SuperAdminMiddleware called', [
            'path' => $request->path(),
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'roles' => Auth::check() ? Auth::user()->getRoleNames() : []
        ]);

        if (Auth::check()) {
            // Bypass permissions for user ID 1 or users with super_admin role
            if (Auth::id() === 1 || Auth::user()->hasRole('super_admin')) {
                \Illuminate\Support\Facades\Log::channel('single')->info('SuperAdminMiddleware access granted', [
                    'user_id' => Auth::id(),
                    'is_id_1' => Auth::id() === 1,
                    'has_role' => Auth::user()->hasRole('super_admin')
                ]);
                return $next($request);
            }
        }

        \Illuminate\Support\Facades\Log::channel('single')->info('SuperAdminMiddleware access denied', [
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
        ]);
        
        // Allow access to admin login routes and assets
        if ($request->is('admin/login') || 
            $request->is('admin/login/*') || 
            $request->is('admin/assets/*') || 
            $request->is('admin/livewire/*') ||
            $request->is('livewire/*') ||
            $request->is('admin/js/*') ||
            $request->is('admin/css/*')) {
            \Illuminate\Support\Facades\Log::channel('single')->info('SuperAdminMiddleware allowing public admin route: ' . $request->path());
            return $next($request);
        }

        // For other routes, show unauthorized
        abort(403, 'Unauthorized action.');
    }
}