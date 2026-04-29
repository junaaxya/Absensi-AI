<?php

namespace App\Traits;

use App\Exceptions\PayrollFrozenException;
use App\Models\PayrollPeriod;
use Carbon\Carbon;

trait PayrollFreezeTrait
{
    public static function bootPayrollFreezeTrait(): void
    {
        static::saving(function ($model) {
            static::checkPayrollFreeze($model);
        });

        static::deleting(function ($model) {
            static::checkPayrollFreeze($model);
        });
    }

    private static function checkPayrollFreeze($model): void
    {
        $dateField = static::getFreezeDateField();
        $dateValue = $model->getAttribute($dateField);

        if (!$dateValue) {
            return;
        }

        $date = Carbon::parse($dateValue);

        $frozen = PayrollPeriod::whereIn('status', ['processing', 'calculated'])
            ->where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->exists();

        if ($frozen) {
            throw new PayrollFrozenException();
        }
    }

    private static function getFreezeDateField(): string
    {
        return match (class_basename(static::class)) {
            'Attendance' => 'tanggal',
            'Violation'  => 'tanggal',
            'Izin'       => 'tanggal_mulai',
            default      => 'tanggal',
        };
    }
}
