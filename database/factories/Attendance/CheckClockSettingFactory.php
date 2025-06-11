<?php

namespace Database\Factories\Attendance;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Org\Company>
 */
class CheckClockSettingFactory extends Factory
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
            'id_company' => $this->faker->uuid(),
            'name' => $this->faker->word() . ' Check Clock Setting',
            'type' => 'WFO',
        ];
    }
}
