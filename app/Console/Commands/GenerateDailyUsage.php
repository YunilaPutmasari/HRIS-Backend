<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\Subscription;
use App\Models\Subscription\DailyUsageRecord;
use Carbon\Carbon;

class GenerateDailyUsage extends Command
{
    protected $signature = 'subscription:generate-daily-usage';
    protected $description = 'Generate daily usage records for active subscriptions';

    public function handle()
    {
        $today = now()->startOfDay();
        $subscriptions = Subscription::where('status', 'active')->get();

        foreach ($subscriptions as $subscription) {
            $packageType = $subscription->packageType;

            if (!$packageType) continue;

            // Harga per seat per hari
            $dailyCostPerSeat = $packageType->price_per_seat / 30; // Asumsi 30 hari dalam bulan
            $totalDailyCost = round($dailyCostPerSeat * $subscription->seats, 0);

            DailyUsageRecord::create([
                'id_company' => $subscription->id_company,
                'id_subscription' => $subscription->id,
                'date' => $today,
                'daily_cost' => $totalDailyCost,
            ]);
        }

        $this->info("Berhasil generate daily usage untuk hari ini.");
        return Command::SUCCESS;
    }
}
