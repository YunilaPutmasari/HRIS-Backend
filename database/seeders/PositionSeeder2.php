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
        // Ambil ID department dari tb_department
        $itDept = DB::table('tb_department')->where('name', 'IT')->where('location','Jakarta')->value('id');
        $hrDept = DB::table('tb_department')->where('name', 'HR')->where('location','Jakarta')->value('id');
        $financeDept = DB::table('tb_department')->where('name', 'Finance')->where('location','Jakarta')->value('id');

        $positions = [];

        if ($itDept) {
            $positions[] = ['id' => (string) Str::uuid(), 'name' => 'Software Engineer', 'level' => 2, 'id_department' => $itDept];
            $positions[] = ['id' => (string) Str::uuid(), 'name' => 'System Analyst', 'level' => 3, 'id_department' => $itDept];
        }

        if ($hrDept) {
            $positions[] = ['id' => (string) Str::uuid(), 'name' => 'HR Specialist', 'level' => 2, 'id_department' => $hrDept];
            $positions[] = ['id' => (string) Str::uuid(), 'name' => 'Recruitment Officer', 'level' => 1, 'id_department' => $hrDept];
        }

        if ($financeDept) {
            $positions[] = ['id' => (string) Str::uuid(), 'name' => 'Accountant', 'level' => 2, 'id_department' => $financeDept];
            $positions[] = ['id' => (string) Str::uuid(), 'name' => 'Finance Staff', 'level' => 1, 'id_department' => $financeDept];
        }

        foreach ($positions as $position) {
            DB::table('tb_position')->insert($position + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}