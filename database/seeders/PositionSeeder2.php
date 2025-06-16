<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PositionSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixedDepartmentId = '2bd9a20d-4946-457e-874d-60174d64bdaf';

        $positions = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Software Engineer',
                'level' => 2,
                'gaji' => 12000000,
                'id_department' => $fixedDepartmentId
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'System Analyst',
                'level' => 3,
                'gaji' => 11000000,
                'id_department' => $fixedDepartmentId
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'HR Specialist',
                'level' => 2,
                'gaji' => 9000000,
                'id_department' => $fixedDepartmentId
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Recruitment Officer',
                'level' => 1,
                'gaji' => 8000000,
                'id_department' => $fixedDepartmentId
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Accountant',
                'level' => 2,
                'gaji' => 9500000,
                'id_department' => $fixedDepartmentId
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Finance Staff',
                'level' => 1,
                'gaji' => 8500000,
                'id_department' => $fixedDepartmentId
            ],
        ];

        foreach ($positions as $position) {
            DB::table('tb_position')->insert($position + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
