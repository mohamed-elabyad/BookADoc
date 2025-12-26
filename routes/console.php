<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('appointments:delete-old')
    ->daily()
    ->withoutOverlapping();

Schedule::command('appointments:delete-unpaid')
    ->everySixHours()
    ->withoutOverlapping();

Schedule::command('appointments:send-reminders')
    ->daily()
    ->withoutOverlapping();
