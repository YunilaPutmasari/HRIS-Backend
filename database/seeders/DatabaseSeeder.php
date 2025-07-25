<?php

namespace Database\Seeders;

use App\Models\Org\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            PackageTypeSeeder::class,
            OrganizationSeeder::class,
            EmployeeSeeder2::class,
            ApprovalSeeder::class,
        ]);
    }
}
