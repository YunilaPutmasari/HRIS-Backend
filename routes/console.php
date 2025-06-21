<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ListSchedules;
use App\Console\Commands\CheckTrialStatus;
use App\Console\Commands\ExpireSubscription;
use App\Console\Commands\RenewSubscription;
use App\Console\Commands\CheckOverdueInvoices;
use App\Console\Commands\GenerateDailyUsage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ini cuma untuk ditampilkan di php artisan schedule:list
if (!defined('APP_SCHEDULE')) {
    define('APP_SCHEDULE', [
        'subscription:check-trial' => 'everyMinutes',
        'subscription:expire' => 'everyMinutes',
        'subscription:renew' => 'every 3 Minutes',
        'subscription:generate-daily-usage' => 'everyMinutes',
        'invoice:check-overdue' => 'every 5 Minutes',
    ]);
}

// Schedule commands
// 1. cek trial
// 2. cek expired
// 3. pengecekan buat invoice (kalau cancel/upgrade/downgrade)
// 4. renew subs
// 5. cek overdue invoice -> masih perlu update
// 6. buat daily usage dari subs
Schedule::command('subscription:check-trial')->everyMinute();
Schedule::command('subscription:expire')->everyMinute();
Schedule::command('invoice:generate-on-event')->everyMinute(); // atau cron sesuai kebutuhan
Schedule::command('subscription:renew')->cron('*/3 * * * *'); //cek 3 menit
Schedule::command('invoice:check-overdue')->cron('*/5 * * * *');
Schedule::command('subscription:generate-daily-usage')->everyMinute();