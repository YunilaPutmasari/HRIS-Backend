<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;

class ExpireSubscription extends Command
{
    protected $signature = 'subscription:expire';

    protected $description = 'Expire subscriptions whose ends_at has passed';

    public function handle()
    {
        $now = Carbon::now();

        $subscrpitions = Subscription::where('ends_at', '<',now())
            ->where('status','active')
            ->get();

        foreach ($subscrpitions as $sub){
            $sub->update(['status'=>'expired']);
            $this->info("Expired subscription ID: {$sub->id}");
        }

        return Command::SUCCESS;
    }
}
