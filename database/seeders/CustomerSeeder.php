<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            "name" => 'user',
            "email" => 'user@example.net',
            "phone" => '01065438133',
            "password" => Hash::make('password'),
            "status" => '1',
        ]);

        Customer::factory(14)->create();
    }
}
