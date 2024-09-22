<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\DiningTable;
use App\Models\Administrator;

class DiningTablesTest extends TestCase
{
    //? success
    public function testIndexSuccess()
    {
        $response = $this->getJson('/api/diningtables');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'floor',
                    'size',
                    'status',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
    }

    //? success
    public function testStoreSuccess()
    {
        $admin = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin-api');

        $DiningTableData = [
            'name' => 'Test Meal',
            "floor" => fake()->numberBetween(1, 500),
            "size" => fake()->randomElement(["1", "2", "3", "4"]),
            "status" => fake()->randomElement(["1", "0"]),
        ];

        $response = $this->postJson('/api/diningtables/store', $DiningTableData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'DiningTable created successfully'
        ]);
    }

    //? success
    public function testShowSuccess()
    {
        $DiningTableData = DiningTable::create([
            'name' => 'Test diningtable',
            "floor" => fake()->numberBetween(1, 500),
            "size" => fake()->randomElement(["1", "2", "3", "4"]),
            "status" => '1',
        ]);

        $response = $this->getJson('/api/diningtables/show/' . $DiningTableData->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    //? success
    public function testUpdateSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $DiningTableData = DiningTable::factory()->create(['status' => '1']);

        $response = $this->postJson("/api/diningtables/update/{$DiningTableData->id}", ['status' => '0']);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'DiningTable updated successfully',
        ]);
    }

    //? success
    public function testUpdateStatusSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $DiningTableData = DiningTable::create([
            'name' => 'Test diningtable',
            "floor" => fake()->numberBetween(1, 500),
            "size" => fake()->randomElement(["1", "2", "3", "4"]),
            "status" => '0',
        ]);

        $response = $this->putJson("/api/diningtables/active/{$DiningTableData->id}", ['status' => '1']);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'DiningTable status updated successfully',
        ]);
    }

    //? success
    public function testDestroySuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $DiningTableData = DiningTable::create([
            'name' => 'Test diningtable',
            "floor" => fake()->numberBetween(1, 500),
            "size" => fake()->randomElement(["1", "2", "3", "4"]),
            "status" => '1',
        ]);

        $response = $this->deleteJson("/api/diningtables/{$DiningTableData->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'DiningTable deleted successfully',
        ]);
    }
}
