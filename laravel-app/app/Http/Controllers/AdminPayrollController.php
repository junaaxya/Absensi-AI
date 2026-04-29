<?php

namespace App\Http\Controllers;

use App\Models\PayrollDetail;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\PayrollService;
use App\Services\SlipGajiPdfService;
use Illuminate\Http\Request;

class AdminPayrollController extends Controller
{
    public function __construct(
        private PayrollService $payrollService,
    ) {}

    public function index()
    {
        $periods = PayrollPeriod::orderByDesc('period_month')->paginate(15);

        return view('admin.payroll.index', compact('periods'));
    }

    public function create()
    {
        return view('admin.payroll.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_month' => 'required|date_format:Y-m|unique:payroll_periods,period_month',
        ]);

        $period = $this->payrollService->createPeriod($request->period_month);

        return redirect()
            ->route('admin.payroll.show', $period)
            ->with('success', 'Periode payroll berhasil dibuat.');
    }

    public function show(PayrollPeriod $period)
    {
        $period->load(['payrollDetails.user.department', 'payrollDetails.items', 'approver']);

        return view('admin.payroll.show', compact('period'));
    }

    public function calculate(PayrollPeriod $period)
    {
        if (!in_array($period->status, ['draft', 'calculated'])) {
            return back()->with('error', 'Periode tidak dapat dihitung ulang pada status ini.');
        }

        $this->payrollService->calculateAll($period);

        return back()->with('success', 'Perhitungan payroll selesai.');
    }

    public function approve(PayrollPeriod $period)
    {
        if ($period->status !== 'calculated') {
            return back()->with('error', 'Hanya periode dengan status "calculated" yang dapat disetujui.');
        }

        $this->payrollService->approvePeriod($period, auth()->user());

        return back()->with('success', 'Periode payroll telah disetujui.');
    }

    public function markPaid(PayrollPeriod $period)
    {
        if ($period->status !== 'approved') {
            return back()->with('error', 'Hanya periode dengan status "approved" yang dapat ditandai lunas.');
        }

        $this->payrollService->markAsPaid($period);

        return back()->with('success', 'Periode payroll telah ditandai sebagai lunas.');
    }

    public function slip(PayrollPeriod $period, User $user)
    {
        $detail = PayrollDetail::where('payroll_period_id', $period->id)
            ->where('user_id', $user->id)
            ->with(['items', 'user.department'])
            ->firstOrFail();

        $settings = \App\Models\SystemSetting::first();

        return view('admin.payroll.slip', compact('detail', 'period', 'settings'));
    }

    public function downloadSlip(PayrollPeriod $period, User $user)
    {
        $detail = PayrollDetail::where('payroll_period_id', $period->id)
            ->where('user_id', $user->id)
            ->with(['items', 'user.department'])
            ->firstOrFail();

        $pdfService = app(SlipGajiPdfService::class);

        return $pdfService->download($detail);
    }
}
