<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Org\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Human Resources', 'location' => 'Jakarta'],
            ['name' => 'IT', 'location' => 'Bandung'],
            ['name' => 'Finance', 'location' => 'Surabaya'],
        ];

        foreach ($departments as $data) {
            Department::create($data);
        }
    }
}
