<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezone
{
    /**
     * Handle an incoming request.
     *
     * Overrides the app-wide default timezone (config('app.timezone'),
     * already applied at boot) with the authenticated user's own
     * preference for the rest of this request, so now()/today's date
     * matches their clock rather than the server's.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = $request->user()?->timezone;

        if ($timezone) {
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }
}
