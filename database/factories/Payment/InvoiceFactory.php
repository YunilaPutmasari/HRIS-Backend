<?php

namespace Database\Factories\Payment;

use App\Models\Payment\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;
    
    public function definition(): array
    {
        return [
            'id_user' => $this->faker->uuid,
            'total_amount' => $this->faker->randomFloat(2, 1, 1000),
            'due_datetime' => $this->faker->dateTimeBetween('now', '+1 year'),
            'status' => $this->faker->randomElement(['paid', 'unpaid', 'failed']),
        ];
    }
}
