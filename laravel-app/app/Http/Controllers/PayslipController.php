<?php

namespace App\Http\Controllers;

use App\Models\PayrollDetail;
use App\Services\SlipGajiPdfService;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index()
    {
        $payslips = PayrollDetail::where('user_id', auth()->id())
            ->whereHas('payrollPeriod', function ($query) {
                $query->whereIn('status', ['paid', 'locked']);
            })
            ->with('payrollPeriod')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('payslips.index', compact('payslips'));
    }

    public function show(PayrollDetail $detail)
    {
        if ($detail->user_id !== auth()->id()) {
            abort(403);
        }

        $detail->load(['items', 'payrollPeriod']);

        $items = $detail->items->groupBy('component_type');

        return view('payslips.show', compact('detail', 'items'));
    }

    public function download(PayrollDetail $detail, SlipGajiPdfService $pdfService)
    {
        if ($detail->user_id !== auth()->id()) {
            abort(403);
        }

        return $pdfService->download($detail);
    }
}
