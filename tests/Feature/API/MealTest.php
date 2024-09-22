<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\Meal;
use App\Models\Administrator;

class MealTest extends TestCase
{
    //? success
    public function testIndexSuccess()
    {
        $response = $this->getJson('/api/meals');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'type',
                    'status',
                    'image',
                    'category_id',
                    'created_at',
                    'updated_at',
                    'category'
                ]
            ]
        ]);
    }

    //? success
    public function testStoreSuccess()
    {
        $admin = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($admin, 'admin-api');

        $mealData = [
            'name' => 'Test Meal',
            'description' => 'Test Description',
            'price' => 10,
            'status' => '1',
            // 'image' => '',
            'category_id' => 1
        ];

        $response = $this->postJson('/api/meals/store', $mealData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Meal created successfully'
        ]);
    }

    //? success
    public function testShowSuccess()
    {
        $meal = Meal::factory()->create(['status' => '1']);

        $response = $this->getJson('/api/meals/show/' . $meal->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    //? success
    public function testUpdateSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $meal = Meal::factory()->create(['status' => '1']);

        $update_data = [
            'name' => 'Updated Meal Name',
            'description' => 'Updated description.',
            'price' => 20,
            'type' => 'vegetarian',
            'status' => '1',
            'category_id' => 1,
        ];

        $response = $this->postJson("/api/meals/update/{$meal->id}", $update_data);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Meal updated successfully',
        ]);
    }

    //? success
    public function testUpdateStatusSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $meal = Meal::factory()->create(['status' => '0']);

        $response = $this->putJson("/api/meals/active/{$meal->id}", ['status' => '1']);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Meal status updated successfully',
        ]);
    }

    //? success
    public function testDestroySuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $meal = Meal::factory()->create(['status' => '1']);

        $response = $this->deleteJson("/api/meals/{$meal->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Meal deleted successfully',
        ]);
    }
}
