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
            'org/2025_05_13_064342_tb_salary.php',
            'org/2025_05_13_064351_tb_payroll.php',

            // attendance migrations
            'attendance/2025_05_13_071748_tb_check_clock_setting.php',
            'attendance/2025_05_13_071739_tb_check_clock_setting_time.php',
            'attendance/2025_05_13_071757_tb_check_clock.php',
            'org/2025_05_05_121230_tb_user_to_tb_check_clock_setting_relation.php',

            // overtime migrations
            'overtime/2025_05_13_072537_tb_overtime_setting.php',
            'overtime/2025_05_13_072548_tb_overtime_setting_rule.php',
            'overtime/2025_05_13_072540_tb_overtime.php',

            // lettering migrations
            'lettering/2025_05_13_074517_tb_letter_format.php',
            'lettering/2025_05_13_074514_tb_letter.php',
            'lettering/2025_05_13_074521_tb_documents.php',
            'lettering/2025_05_13_074529_tb_employee_request.php',

            // subscription migrations
            'subscription/2025_06_14_182027_tb_package_types.php',
            'subscription/2025_05_13_081936_tb_subscription.php',
            'subscription/2025_06_14_184654_tb_pending_change.php',
            'subscription/2025_06_15_132931_tb_daily_usage_records.php',

            // payment migrations
            'payment/2025_05_13_081937_tb_invoice.php',
            'payment/2025_05_13_081938_tb_payment.php',

            // reset password
            '2025_05_31_031804_create_table_password_resets.php',
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