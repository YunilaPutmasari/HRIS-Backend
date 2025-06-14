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
        $fixedDepartmentId = 'dd9c6b13-2227-4a3f-b6a2-2691fb05a70b';

        $positions = [
            ['id' => (string) Str::uuid(), 'name' => 'Software Engineer', 'level' => 2, 'id_department' => $fixedDepartmentId],
            ['id' => (string) Str::uuid(), 'name' => 'System Analyst', 'level' => 3, 'id_department' => $fixedDepartmentId],
            ['id' => (string) Str::uuid(), 'name' => 'HR Specialist', 'level' => 2, 'id_department' => $fixedDepartmentId],
            ['id' => (string) Str::uuid(), 'name' => 'Recruitment Officer', 'level' => 1, 'id_department' => $fixedDepartmentId],
            ['id' => (string) Str::uuid(), 'name' => 'Accountant', 'level' => 2, 'id_department' => $fixedDepartmentId],
            ['id' => (string) Str::uuid(), 'name' => 'Finance Staff', 'level' => 1, 'id_department' => $fixedDepartmentId],
        ];

        foreach ($positions as $position) {
            DB::table('tb_position')->insert($position + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}