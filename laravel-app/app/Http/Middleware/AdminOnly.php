<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->hasAnyRole(['Direktur', 'Vice President', 'Manager', 'Supervisor', 'Team Leader'])) {
            return new Response('Forbidden', 403);
        }

        return $next($request);
    }
}
