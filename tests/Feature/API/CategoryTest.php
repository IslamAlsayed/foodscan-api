<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Administrator;

class CategoryTest extends TestCase
{
    //? success
    public function testIndexSuccess()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'image',
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

        $categoryData = [
            'name' => 'Test Category',
            'description' => 'Test Description',
            // 'image' => '',
            'status' => '1'
        ];

        $response = $this->postJson('/api/categories/store', $categoryData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Category created successfully'
        ]);
    }

    //? success
    public function testShowSuccess()
    {
        $category = Category::create([
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'image' => fake()->imageUrl(),
            'status' => "1"
        ]);

        $response = $this->getJson('/api/categories/show/' . $category->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    //? success
    public function testUpdateSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $category = Category::create([
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'image' => fake()->imageUrl(),
            'status' => "1"
        ]);

        $update_data = [
            'name' => 'Updated Category Name',
            'description' => 'Updated description.',
            'status' => '1',
        ];

        $response = $this->postJson("/api/categories/update/{$category->id}", $update_data);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Category updated successfully',
        ]);
    }

    //? success
    public function testUpdateStatusSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $category = Category::create([
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'image' => fake()->imageUrl(),
            'status' => "0"
        ]);

        $response = $this->putJson("/api/categories/active/{$category->id}", ['status' => '1']);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Category status updated successfully',
        ]);
    }

    //? success
    public function testDestroySuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $category = Category::create([
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'image' => fake()->imageUrl(),
            'status' => "1"
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Category deleted successfully',
        ]);
    }
}
