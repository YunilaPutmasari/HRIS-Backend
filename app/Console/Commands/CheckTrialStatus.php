<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;

class CheckTrialStatus extends Command
{
    protected $signature = 'subscription:check-trial';
    protected $description = 'Check and update subscriptions whose trial has ended';

    public function handle(): int
    {
        $now = Carbon::now();

        $subscriptions = Subscription::where('status', 'trial')
            ->where('trial_ends_at', '<=', $now)
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'status' => 'expired', // atau pending_payment
            ]);

            // Log atau notifikasi opsional
            $this->info("Subscription {$subscription->id} expired from trial.");
        }

        return Command::SUCCESS;
    }
}

