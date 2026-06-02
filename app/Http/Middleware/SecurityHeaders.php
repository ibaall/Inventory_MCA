<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Tambahkan security headers ke setiap response.
     * Melindungi dari XSS, clickjacking, MIME sniffing, dll.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cegah clickjacking (iframe embedding)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Cegah MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Aktifkan XSS protection bawaan browser
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy - batasi info yang dikirim ke situs lain
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy - batasi akses kamera, mikrofon, dll
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Strict Transport Security (HTTPS only) - aktif di production
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
