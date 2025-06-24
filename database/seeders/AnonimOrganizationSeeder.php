<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Org\Company;
use App\Models\Org\User;
use App\Models\Org\Employee;
use App\Models\Subscription\PackageType;
use App\Models\Subscription\Subscription;

class AnonimOrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $existingUser = User::where('email', 'AnonimTester@email.com')->first();

        if ($existingUser) {
            $existingEmployee = Employee::where('id_user', $existingUser->id)->first();
            if ($existingEmployee) {
                $existingEmployee->delete();
            }

            $existingCompany = Company::where('id_manager', $existingUser->id)
                ->where('name', 'Anonim Corp')->first();
            if ($existingCompany) {
                $existingSubscription = Subscription::where('id_company', $existingCompany->id)->first();
                if ($existingSubscription) {
                    $existingSubscription->delete();
                }

                $existingCompany->delete();
            }

            $existingUser->delete();
        }
        
        $this->call([
            // seeder package subscription
            PackageTypeSeeder::class,
            // seeder organization Anonim Corp
            OrganizationSeeder2::class,
            EmployeeSeeder2::class,
            DepartmentSeeder3::class,
            PositionSeeder4::class,
            ApprovalSeeder::class,
            // seeder checkclock
            CheckClockSettingSeeder::class,
            CheckClockSeeder::class,
        ]);
    }
}
