<?php

namespace Database\Seeders;

use App\Models\Administrator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Administrator::create([
            "name" => 'admin',
            "email" => 'admin@example.net',
            "phone" => '01065438133',
            "password" => Hash::make('password'),
            "status" => '1',
        ]);

        Administrator::factory(14)->create();
    }
}
