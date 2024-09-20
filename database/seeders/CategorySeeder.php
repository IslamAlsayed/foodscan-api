<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $categories = [
        "Appetizers",
        "Flame Grill Burgers",
        "Veggie & Plant Based Burgers",
        "Sandwich From The Grill",
        "Hot Chicken Entrees",
        "Beef Entrees",
        "Seafood Entrees",
        "House Special Salads",
        "Zoop Soups",
        "Side Orders",
        "Beverages",
        "Hot Drinks",
        "Iced Drinks",
    ];

    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::create([
                'name' => $category,
                'description' => fake()->sentence(),
                'image' => fake()->imageUrl(),
                'status' => fake()->randomElement(["1", "0"]),
            ]);
        }
    }
}
