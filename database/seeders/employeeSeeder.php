<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Org\Employee;
use App\Models\Org\User;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::factory()->count(10)->create(); // bikin 10 data employee lengkap dengan user-nya
    }

    public function definition(): array
    {
        return [
            'id_user' => User::factory(), // ini yang penting!
            'sign_in_code' => $this->faker->unique()->numerify('######'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'address' => $this->faker->address,
            'employment_status' => $this->faker->randomElement(['active', 'resign']),
            'id_position' => null, // isi sesuai kebutuhanmu
        ];
    }
}
