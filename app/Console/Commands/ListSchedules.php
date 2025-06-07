<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class ListSchedules extends Command
{
    protected $signature = 'schedule:list';
    protected $description = 'Debug: Show all scheduled commands';

    public function handle()
    {
        if (defined('APP_SCHEDULE')) {
            $schedules = collect(APP_SCHEDULE)->map(fn($type, $command) => [
                'Command' => $command,
                'Type' => $type,
                'Description' => 'Scheduled task',
            ]);

            $this->table(['Command', 'Type'], $schedules->toArray());
        } else {
            $this->warn("No schedules defined.");
        }

        return Command::SUCCESS;
    }
}