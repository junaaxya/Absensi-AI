<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('cleanup:old-data')->daily()->at('02:00');
Schedule::command('violations:generate-alpha')->dailyAt('23:00');
Schedule::command('attendance:auto-checkout')->dailyAt('23:55');
