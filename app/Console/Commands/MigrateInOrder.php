<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateInOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-in-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute the migrations in the order specified in the file app/Console/Comands/MigrateInOrder.php \n Drop all the table in db before execute the command.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrations = [

            '2025_05_05_142003_extensions.php',

            // laravel default migrations
            '0001_01_01_000001_create_cache_table.php',
            '0001_01_01_000002_create_jobs_table.php',

            // organization structure migrations
            'org/0001_01_01_000000_tb_user.php',
            'org/2025_05_05_113249_tb_company.php',
            'org/2025_05_05_121230_tb_user_to_tb_company_relation.php',
            'org/2025_05_05_113301_tb_department.php',
            'org/2025_05_05_113257_tb_position.php',
            'org/2025_05_05_113254_tb_employee.php',
            'org/2025_05_05_133704_create_personal_access_tokens_table.php',
        ];

        // set foreign key check to 0
        if (env('DB_CONNECTION') == 'pgsql')
            DB::statement("SET session_replication_role = 'replica';");

        // drop all the tables
        $this->call('db:wipe');

        // execute the migrations
        foreach ($migrations as $migration) {
            $this->call('migrate:refresh', [
                '--path' => "database/migrations/{$migration}"
            ]);
        }

        // set foreign key check to 1
        if (env('DB_CONNECTION') == 'pgsql')
            DB::statement("SET session_replication_role = 'origin';");

    }
}