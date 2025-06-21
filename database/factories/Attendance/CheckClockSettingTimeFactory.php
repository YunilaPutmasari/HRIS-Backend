<?php

namespace Database\Factories\Attendance;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Org\Company>
 */
class CheckClockSettingTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_ck_setting' => fake()->uuid(),
            'day' => fake()->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'clock_in' => fake()->time('H:i'),
            'clock_out' => fake()->time('H:i'),
            'break_start' => fake()->time('H:i'),
            'break_end' => fake()->time('H:i'),
        ];
    }
}
