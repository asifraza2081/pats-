<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InactivityLogout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity');
            // Convert minutes to seconds

            // default to 15 mins if not set specifically, but session.lifetime is usually 120
            // We want stricter 15m for examiners/admins
            $inactivityLimit = 15 * 60; 

            if ($lastActivity && (time() - $lastActivity > $inactivityLimit)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Session expired due to inactivity. Please login again.');
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
