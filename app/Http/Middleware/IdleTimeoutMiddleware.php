<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdleTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $lastActivity = $request->session()->get('last_activity');

        if ($lastActivity !== null && now()->diffInSeconds($lastActivity, true) > 900) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['idle' => 'Sesi berakhir karena tidak ada aktivitas.']);
        }

        $request->session()->put('last_activity', now());

        return $next($request);
    }
}
