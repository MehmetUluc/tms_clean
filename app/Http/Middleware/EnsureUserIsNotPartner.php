<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotPartner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated and has partner role, deny access to admin panel
        if (auth()->check() && auth()->user()->hasRole('partner')) {
            // Redirect to partner panel
            return redirect('/partner');
        }

        return $next($request);
    }
}