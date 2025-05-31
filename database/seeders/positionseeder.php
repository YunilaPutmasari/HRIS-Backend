<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run()
    {
        // Ambil department id dari database
        $departments = DB::table('tb_department')->get()->keyBy('name');

        $positions = [
            ['id' => Str::uuid(), 'name' => 'HR Manager', 'level' => 'Senior', 'id_department' => $departments['Human Resources']->id, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => 'Recruiter', 'level' => 'Junior', 'id_department' => $departments['Human Resources']->id, 'created_at' => now(), 'updated_at' => now()],

            ['id' => Str::uuid(), 'name' => 'IT Manager', 'level' => 'Senior', 'id_department' => $departments['IT']->id, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => 'Developer', 'level' => 'Junior', 'id_department' => $departments['IT']->id, 'created_at' => now(), 'updated_at' => now()],

            ['id' => Str::uuid(), 'name' => 'Finance Manager', 'level' => 'Senior', 'id_department' => $departments['Finance']->id, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => 'Accountant', 'level' => 'Junior', 'id_department' => $departments['Finance']->id, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('tb_position')->insert($positions);
    }
}
