<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PositionSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixedDepartmentId = '642f2982-2eb0-48bd-b165-a4444c6aa58a';

        $positions = [
            ['id' => $fixedDepartmentId, 'name' => 'Software Engineer', 'level' => 2, 'id_department' => $fixedDepartmentId],
            ['id' => $fixedDepartmentId, 'name' => 'System Analyst', 'level' => 3, 'id_department' => $fixedDepartmentId],
            ['id' => $fixedDepartmentId, 'name' => 'HR Specialist', 'level' => 2, 'id_department' => $fixedDepartmentId],
            ['id' => $fixedDepartmentId, 'name' => 'Recruitment Officer', 'level' => 1, 'id_department' => $fixedDepartmentId],
            ['id' => $fixedDepartmentId, 'name' => 'Accountant', 'level' => 2, 'id_department' => $fixedDepartmentId],
            ['id' => $fixedDepartmentId, 'name' => 'Finance Staff', 'level' => 1, 'id_department' => $fixedDepartmentId],
        ];

        foreach ($positions as $position) {
            DB::table('tb_position')->insert($position + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
