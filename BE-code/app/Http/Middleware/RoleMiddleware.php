<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RedirectIfNotCandidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /**
         * check role
         */
        if (Auth::check() && Auth::user()->role === 'candidate') {
            return $next($request);
        } elseif (Auth::check() && Auth::user()->role === 'company') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized.'], 401);
    }
}
