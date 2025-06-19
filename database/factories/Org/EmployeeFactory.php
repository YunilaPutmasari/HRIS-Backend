<?php


namespace Database\Factories\Org;

use App\Models\Org\Employee;
use App\Models\Org\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [

            'id_user' => $this->faker->uuid,
            'sign_in_code' => $this->faker->unique()->randomNumber(6, true),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'address' => $this->faker->address(),
            'employment_status' => $this->faker->randomElement(['active', 'inactive', 'resign']),
            'id_position' => null, // sesuaikan kalau perlu
            // Kolom baru yang kamu tambahkan
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'phone_number' => $this->faker->phoneNumber(),
            'cabang' => $this->faker->city(),
            // 'position' => $this->faker->jobTitle(),
            'id_position' => null,

        ];
    }
}
