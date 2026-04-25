<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy
        // Note: Allowing 'unsafe-inline' and 'unsafe-eval' for Tabler/AlpineJS/TomSelect compatibility
        $csp = "default-src 'self'; ";
        $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; ";
        $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; ";
        $csp .= "font-src 'self' data:; ";
        $csp .= "img-src 'self' data: https://* http://*; ";
        $csp .= "frame-ancestors 'none'; ";
        $csp .= "form-action 'self'; ";
        $csp .= "base-uri 'self'; ";
        $csp .= "connect-src 'self' https://*; ";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
