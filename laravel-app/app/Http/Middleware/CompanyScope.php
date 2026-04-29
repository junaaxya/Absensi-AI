<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->company_id) {
            $canSwitchCompany = $user->hasAnyRole(['Direktur', 'Vice President']);

            if (! $canSwitchCompany) {
                $request->merge(['scoped_company_id' => $user->company_id]);
            }
        }

        return $next($request);
    }
}
