<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ListSchedules;
use App\Console\Commands\CheckTrialStatus;
use App\Console\Commands\ExpireSubscription;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (!defined('APP_SCHEDULE')) {
    define('APP_SCHEDULE', [
        'subscription:check-trial' => 'everyMinute',
        'subscription:expire' => 'daily',
    ]);
}

Schedule::command('subscription:check-trial')->everyMinute();
Schedule::command('subscription:expire')->daily();