<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kullanıcı oturum açmamışsa 
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        // Super admin'e her zaman izin verilir (ID=1)
        if (auth()->id() === 1) {
            return $next($request);
        }
        
        // Admin yetkisine sahip kullanıcılara izin verilir
        if (auth()->user()->isAdmin()) {
            return $next($request);
        }
        
        // Rol ve yetki kontrolü
        if (auth()->user()->can('view_admin_panel')) {
            return $next($request);
        }
        
        // Eğer buraya kadar geldiyse, kullanıcının yetkisi yok
        abort(403, 'Bu alana erişim yetkiniz yok.');
    }
}