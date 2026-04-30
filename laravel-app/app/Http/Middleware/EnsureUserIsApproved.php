<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_approved) {
            if ($request->routeIs('pending-approval') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('pending-approval');
        }

        return $next($request);
    }
}
