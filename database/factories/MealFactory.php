<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "description" => fake()->sentence(),
            "price" => fake()->numberBetween(1, 24),
            "status" => fake()->randomElement(["1", "0"]),
            "image" => fake()->imageUrl(),
            // "category_id" => Category::inRandomOrder()->first()->id,
            "category_id" => 1,
        ];
    }
}
