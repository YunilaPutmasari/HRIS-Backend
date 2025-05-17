<?php


namespace Database\factories\Org;
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
            'id_user' => User::factory(), // generate user otomatis
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'address' => $this->faker->address(),
            'employment_status' => $this->faker->randomElement(['active', 'inactive', 'resign']),
            'id_position' => null, // nanti bisa disesuaikan
        ];
    }
}
