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
                'is_trial' => false,
                'status' => 'active', // atau pending_payment
                'starts_at' => now(),
                'ends_at' => now()->addMinutes(10),
            ]);

            // Log atau notifikasi opsional
            $this->info("Subscription {$subscription->id} trial expired dan menjadi aktif");
        }

        return Command::SUCCESS;
    }
}

