<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdministratorSeeder::class,
            EmployeeSeeder::class,
            CategorySeeder::class,
            MealSeeder::class,
            AddonSeeder::class,
            ExtraSeeder::class,
            CustomerSeeder::class,
            DiningTableSeeder::class,
        ]);
    }
}
