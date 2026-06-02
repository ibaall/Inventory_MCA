<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Paksa redirect ke HTTPS di production.
     * Hanya aktif ketika APP_ENV=production.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production') && !$request->isSecure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
