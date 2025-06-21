<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\Org\Department;
use App\Models\Org\Position;
use App\Models\Attendance\CheckClockSetting;
use App\Models\Attendance\CheckClockSettingTime;
use Illuminate\Database\Seeder;

use App\Models\Org\Company;
use App\Models\Org\Employee;
use App\Models\Org\User;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // truncate all tables
        Employee::truncate();
        Position::truncate();
        Department::truncate();
        Company::truncate();
        User::truncate();


        $supreme_manager_user = User::factory()->create([
            'email' => 'manager@cmlabs.com',
            'phone_number' => '+6281234567890',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $company = Company::factory()->create([
            'name' => 'PT cmlabs Indonesia Digital',
            'id_manager' => $supreme_manager_user->id,
        ]);

        Employee::factory()->create([
            'id_user' => $supreme_manager_user->id,
            'sign_in_code' => '999999',
            'first_name' => 'Manager',
            'last_name' => 'Cmlabs',
            'address' => 'Jakarta',
            'employment_status' => 'active',
            'id_position' => null,
            'jenis_kelamin' => 'Laki-laki',
        ]);

        $check_clock_setting = CheckClockSetting::factory()->create([
            'name' => 'Default Check Clock Setting',
            'id_company' => $company->id,
            'type' => 'WFO',
        ]);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        foreach ($days as $day) {
            CheckClockSettingTime::factory()->create([
                'id_ck_setting' => $check_clock_setting->id,
                'day' => $day,
                'clock_in' => '09:00',
                'clock_out' => '17:00',
                'break_start' => '12:00',
                'break_end' => '13:00',
            ]);
        }

        $supreme_manager_user->update([
            'id_workplace' => $company->id,
            'id_check_clock_setting' => $check_clock_setting->id,
        ]);

        $departments = [
            'HR' => [
                'HR Manager' => 1,
                'HR Staff' => 2,
                'Recruitment Staff' => 3,
                'Training Staff' => 4,
            ],
            'Finance' => [
                'Finance Manager' => 1,
                'Finance Staff' => 2,
                'Accounting Staff' => 2,
                'Tax Staff' => 2,
            ],
            'IT' => [
                'IT Manager' => 1,
                'Project Manager' => 2,
                'System Analyst' => 3,
                'Programmer' => 3,
                'UI/UX Designer' => 3,
            ],
            'Marketing' => [
                'Marketing Manager' => 1,
                'Marketing Staff' => 2,
                'Sales Staff' => 2,
                'Customer Service' => 3,
            ],
        ];

        foreach ($departments as $department => $positions) {
            $departmentModel = $company->departments()->create([
                'name' => $department,
                'location' => 'Malang',
            ]);

            foreach ($positions as $position => $level) {
                $positionModel = $departmentModel->positions()->create([
                    'name' => $position,
                    'level' => $level,
                ]);


                for ($i = 1; $i <= 3; $i++) {

                    $user_aux = User::factory()->create([
                        'email' => strtolower(str_replace(' ', '', $position)) . $i . '@cmlabs.com',
                        'password' => bcrypt('password'),
                        'is_admin' => false,
                    ]);

                    Employee::factory()->create([
                        'id_user' => $user_aux->id,
                        'employment_status' => 'active',
                    ]);

                    Approval::factory()->create([
                        'id_user' => $user_aux->id_user,
                    ]);
                }
            }
        }
    }
}
