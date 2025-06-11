<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\Org\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Approval>
 */
class ApprovalFactory extends Factory
{
    protected $model = Approval::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user' => $this->faker->uuid,
            'request_type' => $this->faker->randomElement(['overtime', 'permit', 'sick', 'leave']),
            'start_date' => $this->faker->dateTimeBetween('-1 month'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'reason' => $this->faker->sentence(),
            'status' => 'pending', // Default status
            'approved_by' => null,
        ];
    }
}
