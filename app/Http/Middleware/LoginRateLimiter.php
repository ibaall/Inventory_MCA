<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiter
{
    /**
     * Batasi percobaan login: maksimal 5 kali.
     * Jika gagal 5 kali, harus menunggu 5 jam.
     * Mencegah brute-force attack pada halaman login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rateLimiter = app(RateLimiter::class);
        $key = 'login-attempt:' . $request->ip();
        $maxAttempts = 5;
        $decaySeconds = 18000; // 5 jam = 5 x 3600

        if ($rateLimiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $rateLimiter->availableIn($key);
            $hours = floor($seconds / 3600);
            $minutes = ceil(($seconds % 3600) / 60);

            if ($hours > 0) {
                $waitMessage = "{$hours} jam {$minutes} menit";
            } else {
                $waitMessage = "{$minutes} menit";
            }

            return back()->withErrors([
                'email' => "Akun dikunci karena terlalu banyak percobaan login. Silakan tunggu {$waitMessage} lagi.",
            ])->onlyInput('email');
        }

        $rateLimiter->hit($key, $decaySeconds);

        $response = $next($request);

        // Reset counter jika login berhasil
        if ($response->isRedirection() && !$response->isRedirect(route('login'))) {
            $rateLimiter->clear($key);
        }

        return $response;
    }
}

