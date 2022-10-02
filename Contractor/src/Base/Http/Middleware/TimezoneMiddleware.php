<?php

 namespace Contractor\Base\Http\Middleware;


use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class TimezoneMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('timezone')){
            config()->set('app.timezone', $request->header('timezone'));
            date_default_timezone_set($request->header('timezone'));
        }

        return $next($request);
    }
}
