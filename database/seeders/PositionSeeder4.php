<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PositionSeeder4 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua departemen yang sudah disediakan oleh DepartmentSeeder3
        $departments = DB::table('tb_department')->get();

        // Daftar posisi berdasarkan nama departemen
        $positionData = [
            'IT' => [
                ['name' => 'Software Engineer', 'level' => 2],
                ['name' => 'System Analyst', 'level' => 3],
                ['name' => 'IT Support', 'level' => 1],
            ],
            'HR' => [
                ['name' => 'HR Specialist', 'level' => 2],
                ['name' => 'Recruitment Officer', 'level' => 1],
            ],
            'Finance' => [
                ['name' => 'Accountant', 'level' => 2],
                ['name' => 'Finance Staff', 'level' => 1],
            ],
            'Marketing' => [
                ['name' => 'Marketing Manager', 'level' => 3],
                ['name' => 'Content Creator', 'level' => 1],
            ],
            'Sales' => [
                ['name' => 'Sales Manager', 'level' => 3],
                ['name' => 'Sales Executive', 'level' => 1],
            ],
        ];

        // Masukkan posisi berdasarkan departemen yang cocok
        foreach ($departments as $department) {
            $positions = $positionData[$department->name] ?? [];

            foreach ($positions as $position) {
                DB::table('tb_position')->insert([
                    'id' => (string) Str::uuid(),
                    'name' => $position['name'],
                    'level' => $position['level'],
                    'id_department' => $department->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
