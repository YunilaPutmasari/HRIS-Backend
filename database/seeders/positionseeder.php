<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Org\Position;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            [
                'id' => 'de333a3f-2428-4270-a7eb-7bd52a54ab65',
                'name' => 'HR Manager',
                'level' => 'Senior',
                'id_department' => 'e1c6c8a8-2ab5-4fe6-8942-4da310d1c201',// Human Resources - Jakarta
                'gaji' => 12000000
            ],
            [
                'id' => '30aa562e-207a-4382-a3da-348473de63e5',
                'name' => 'Recruiter',
                'level' => 'Junior',
                'id_department' => 'e1c6c8a8-2ab5-4fe6-8942-4da310d1c201',// Human Resources - Jakarta
                'gaji' => 6000000
            ],
            [
                'id' => '160ded09-3413-4fb1-94b8-9df6b3caa7d7',
                'name' => 'IT Manager',
                'level' => 'Senior',
                'id_department' => '0cdeb6ce-c8c2-4cc0-bede-25f662ea4cc0', // IT - Bandung
                'gaji' => 13000000
            ],
            [
                'id' => '3c5c7b51-823e-431c-8838-9baa816fb6f1',
                'name' => 'Developer',
                'level' => 'Junior',
                'id_department' => '0cdeb6ce-c8c2-4cc0-bede-25f662ea4cc0', // IT - Bandung
                'gaji' => 7000000
            ],
            [
                'id' => '9e16752c-9b88-4711-a4f7-4dceb06eebc9',
                'name' => 'Finance Manager',
                'level' => 'Senior',
                'id_department' => '2f3bb034-30d0-43c0-9685-a84a29aee806', // Finance - Surabaya
                'gaji' => 12500000
            ],
            [
                'id' => '50c7cca3-c9f4-4686-adab-7118121635ee',
                'name' => 'Accountant',
                'level' => 'Junior',
                'id_department' => '2f3bb034-30d0-43c0-9685-a84a29aee806', // Finance - Surabaya
                'gaji' => 6500000
            ],
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(['id' => $position['id']], $position);
        }
    }
}
