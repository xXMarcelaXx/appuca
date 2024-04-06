<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IpsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $allowedIps = ['10.8.0.7', '10.8.0.8', '10.8.0.10'];
        $requestIp = $request->ip();

        if (in_array($requestIp, $allowedIps) && Auth::user()->hasRole('admin')) {
            return $next($request);
        }

        if (Auth::user()->hasRole('invitado') || Auth::user()->hasRole('coordinador')){
            return $next($request);
        }



        return abort(403);
    }
}
