<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            "name" => 'casher',
            "email" => 'casher@example.net',
            "phone" => '01065438133',
            "password" => Hash::make('password'),
            "status" => '1',
        ]);

        Employee::factory(14)->create();
    }
}
