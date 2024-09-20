<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\DiningTable;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $products = [
            ['id' => 1, 'quantity' => 3],
            ['id' => 2, 'quantity' => 2],
            ['id' => 3, 'quantity' => 5]
        ];

        return [
            'total' => fake()->numberBetween(14, 500),
            'notes' => fake()->sentence(),
            'products' => json_encode($products),
            'status' => fake()->randomElement(['1', '0']),
            'employee_id' => Employee::inRandomOrder()->first()->id,
            'dining_table_id' => DiningTable::inRandomOrder()->first()->id,
        ];
    }
}
