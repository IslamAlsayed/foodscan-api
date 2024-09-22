<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DiningTable>
 */
class DiningTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            "floor" => fake()->numberBetween(1, 500),
            "size" => fake()->randomElement(["1", "2", "3", "4"]),
            "status" => fake()->randomElement(["1", "0"]),
        ];
    }
}