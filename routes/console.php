<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ListSchedules;
use App\Console\Commands\CheckTrialStatus;
use App\Console\Commands\ExpireSubscription;
use App\Console\Commands\RenewSubscription;
use App\Console\Commands\CheckOverdueInvoices;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ini cuma untuk ditampilkan di php artisan schedule:list
if (!defined('APP_SCHEDULE')) {
    define('APP_SCHEDULE', [
        'subscription:check-trial' => 'everyMinute',
        'subscription:expire' => 'everyMinute',
        'subscription:renew' => 'everyMinute',
        'invoice:check-overdue' => 'everyMinute',
    ]);
}

// Schedule commands
Schedule::command('subscription:check-trial')->everyMinute();
Schedule::command('subscription:expire')->everyMinute();
Schedule::command('subscription:renew')->everyMinute();
Schedule::command('invoice:check-overdue')->everyMinute();