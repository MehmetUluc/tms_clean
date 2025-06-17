<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'ensure.admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
        
        // Tüm istekler için TrustProxies middleware'ini ekle
        $middleware->trustProxies(at: '*');
        
        // Debug middleware - tüm istekler için
        $middleware->append(\App\Http\Middleware\DebugMiddleware::class);
        
        // Agency session middleware'i kaldırıldı - panel'lere giriş sorununa neden oluyordu
        
        // Inertia SSR için middleware ekle - sadece Inertia rotaları için
        // Filament panel'leriyle çakışmayı önlemek için append yerine rotaya özel olarak eklenecek
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            // Log all exceptions with full details
            try {
                $logData = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
                
                // Only add request data if we're in HTTP context
                if (app()->runningInConsole() === false && request() && !app()->runningUnitTests()) {
                    try {
                        $logData['url'] = request()->fullUrl();
                        $logData['method'] = request()->method();
                        $logData['ip'] = request()->ip();
                        $logData['user_id'] = auth()->id();
                    } catch (\Exception $requestException) {
                        // Ignore request data if it fails
                    }
                }
                
                \Log::error('Exception occurred', $logData);
            } catch (\Exception $loggingException) {
                // Prevent infinite loop if logging fails
            }
        });
    })->create();
