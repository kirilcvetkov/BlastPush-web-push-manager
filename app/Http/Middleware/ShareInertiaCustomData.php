<?php

namespace App\Http\Middleware;

use Inertia\Inertia;

class ShareInertiaCustomData
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        $sidebarIconsOnly = $_COOKIE['sidebarIconsOnly'] ?? true;

        Inertia::share([
            'menu' => config('menu'),
            'countries' => config('countries'),
            'sidebarIconsOnly' => (bool)$sidebarIconsOnly,
            'sidebarActive' => false,
        ]);

        return $next($request);
    }
}
