<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Org\Department;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Ambil data company dari tb_company
        $company = DB::table('tb_company')->first();

        // Pastikan ada data company
        if (!$company) {
            $this->command->error('Tidak ada data company. Jalankan CompanySeeder terlebih dahulu.');
            return;
        }
        $departments = [
            [
                'id' => Str::uuid(),
                'id_company' => $company->id,
                'name' => 'Human Resources',
                'location' => 'Jakarta'
            ],
            [
                'id' => Str::uuid(),
                'id_company' => $company->id,
                'name' => 'IT',
                'location' => 'Bandung'
            ],
            [
                'id' => Str::uuid(),
                'id_company' => $company->id,
                'name' => 'Finance',
                'location' => 'Surabaya'
            ],
        ];

        foreach ($departments as $data) {
            Department::create($data);
        }
    }
}
