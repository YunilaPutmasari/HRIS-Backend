<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = '01976c37-6ff8-73d9-80a1-1a7452e0d7fe'; // Ganti dengan ID company sesuai kebutuhan

        $departments = [
            ['name' => 'IT', 'location' => 'Jakarta'],
            ['name' => 'HR', 'location' => 'Jakarta'],
            ['name' => 'Finance', 'location' => 'Jakarta'],
            ['name' => 'Marketing', 'location' => 'Jakarta'],
            ['name' => 'Sales', 'location' => 'Bandung'],
        ];

        foreach ($departments as $dept) {
            DB::table('tb_department')->insert([
                'id' => (string) Str::uuid(),
                'name' => $dept['name'],
                'location' => $dept['location'],
                'id_company' => $companyId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}