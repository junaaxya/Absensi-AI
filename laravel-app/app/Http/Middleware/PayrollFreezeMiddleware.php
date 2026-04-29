<?php

namespace App\Http\Middleware;

use App\Models\PayrollPeriod;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollFreezeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        $dateFields = ['tanggal', 'tanggal_mulai', 'date'];
        $dateValue = null;

        foreach ($dateFields as $field) {
            if ($request->has($field)) {
                $dateValue = $request->input($field);
                break;
            }
        }

        if ($dateValue) {
            $frozen = PayrollPeriod::whereIn('status', ['processing', 'calculated'])
                ->where('start_date', '<=', $dateValue)
                ->where('end_date', '>=', $dateValue)
                ->exists();

            if ($frozen) {
                return response()->json([
                    'message' => 'Data tidak dapat diubah karena periode payroll sedang diproses atau sudah dihitung.',
                ], 423);
            }
        }

        return $next($request);
    }
}
