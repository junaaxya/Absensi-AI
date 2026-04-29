<?php

// REQUIRES: composer require barryvdh/laravel-dompdf

namespace App\Services;

use App\Models\PayrollDetail;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class SlipGajiPdfService
{
    public function generate(PayrollDetail $detail): \Barryvdh\DomPDF\PDF
    {
        $detail->load(['items', 'user.department', 'payrollPeriod']);
        $settings = SystemSetting::first();

        return Pdf::loadView('pdf.slip-gaji', compact('detail', 'settings'))
            ->setPaper('a4', 'portrait');
    }

    public function download(PayrollDetail $detail): \Symfony\Component\HttpFoundation\Response
    {
        $detail->load(['user', 'payrollPeriod']);
        $filename = 'slip-gaji-' . str_replace(' ', '-', strtolower($detail->user->name))
            . '-' . $detail->payrollPeriod->period_month . '.pdf';

        return $this->generate($detail)->download($filename);
    }
}
