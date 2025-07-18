<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'url' => config('app.url'),
                'isSsr' => app()->has('inertia.ssr.enabled') ? app('inertia.ssr.enabled') : false,
            ],
            'serverTime' => now(),
        ]);
    }
    
    /**
     * Enables Server Side Rendering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function ssr(Request $request): bool
    {
        if (! config('app.ssr_enabled', false)) {
            return false;
        }
        
        $ssrPort = config('app.ssr_port', 13714);
        return $this->renderUsing(
            'http://127.0.0.1:' . $ssrPort
        );
    }
}