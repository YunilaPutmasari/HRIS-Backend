<?php

namespace Database\Factories\Org;

use App\Models\Org\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Org\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->company(),
            'id_manager' => $this->faker->uuid(),
            'effective_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'id_subscription' => null,
        ];
    }
}
