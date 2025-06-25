<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Org\Company;

class DepartmentSeeder3 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $companyName = 'Anonim Corp';
        $company = Company::where('name',$companyName)->first();
        if (!$company) {
            $this->command->error("Company dengan nama '{$companyName}' tidak ditemukan.");
            return;
        }

        $companyId = $company->id;

        $departments = [
            ['name' => 'IT', 'location' => 'Jakarta'],
            ['name' => 'HR', 'location' => 'Jakarta'],
            ['name' => 'Finance', 'location' => 'Jakarta'],
            ['name' => 'Marketing', 'location' => 'Jakarta'],
            ['name' => 'Sales', 'location' => 'Jakarta'],
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