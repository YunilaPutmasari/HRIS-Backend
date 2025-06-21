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
        $companyId = '019776f3-c8e4-71f4-863a-e148ffc43ff9'; // Ganti dengan ID company sesuai kebutuhan

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