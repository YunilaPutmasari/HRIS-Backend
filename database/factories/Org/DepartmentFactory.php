<?php

namespace Database\Factories\Org;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Org\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_company' => null,
            'name' => $this->faker->randomElement(['HR', 'IT', 'Finance', 'Marketing']),
            'location' => $this->faker->city(),
        ];
    }
}
