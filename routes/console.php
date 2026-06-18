<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('attendance:send-missing-reminders check-in')->dailyAt('10:00');
Schedule::command('attendance:send-missing-reminders check-out')->dailyAt('18:00');
