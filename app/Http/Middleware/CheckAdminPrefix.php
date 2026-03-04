<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPrefix
{
    /**
     * Handle an incoming request.
     * When ADMIN_PREFIX is set to a custom value, requests to the literal /admin path return 404 to hide the panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredPrefix = config('app.admin_prefix', 'admin');

        // If a custom admin prefix is set, block access to the default /admin path
        if ($configuredPrefix !== 'admin') {
            $path = trim($request->path(), '/');
            if ($path === 'admin' || str_starts_with($path, 'admin/')) {
                abort(404);
            }
        }

        return $next($request);
    }
}
