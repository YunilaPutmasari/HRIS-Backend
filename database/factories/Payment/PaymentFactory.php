<?php

namespace Database\Factories\Payment;

use App\Models\Payment\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            //
            'id_invoice' => $this->faker->uuid,
            'payment_code' => $this->faker->unique()->randomNumber(6, true),
            'amount_paid' => $this->faker->randomFloat(2, 1, 1000),
            'currency' => $this->faker->currencyCode,
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'e_wallet']),
            'status' => $this->faker->randomElement(['success', 'failed']),
            'payment_datetime' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
