<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ListSchedules;
use App\Console\Commands\CheckTrialStatus;
use App\Console\Commands\ExpireSubscription;
use App\Console\Commands\RenewSubscription;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ini cuma untuk ditampilkan di php artisan schedule:list
if (!defined('APP_SCHEDULE')) {
    define('APP_SCHEDULE', [
        'subscription:check-trial' => 'daily',
        'subscription:expire' => 'daily',
        'subscription:renew' => 'daily'
    ]);
}

// Schedule commands
Schedule::command('subscription:check-trial')->daily();
Schedule::command('subscription:expire')->daily();
Schedule::command('subscription:renew')->daily();