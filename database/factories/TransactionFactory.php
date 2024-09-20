<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "amount" => fake()->numberBetween(1, 500),
            "payment_type" => fake()->randomElement(["cashed", "online", "unpaid"]),
            "order_id" => Order::inRandomOrder()->first()->id,
            "customer_id" => Customer::inRandomOrder()->first()->id,
        ];
    }
}