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
        if (Auth::user()->hasRole('admin') && $request->ip() == '10.8.0.2') {
            return $next($request);
        } else
        if (Auth::user()->hasRole('coordinador')) {
            return $next($request);
        }
        if (Auth::user()->hasRole('invitado') && $request->ip() == '10.8.0.2') {
            return abort(403);
        }
        return abort(403);
    }
}
