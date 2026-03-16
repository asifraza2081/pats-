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

            // Role-based inactivity limits
            if (Auth::user()->hasAnyRole(['admin', 'super_admin', 'data_entry', 'examiner'])) {
                $inactivityLimit = 15 * 60; // 15 mins for staff
            } else {
                $inactivityLimit = 60 * 60; // 60 mins for candidates
            }

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
