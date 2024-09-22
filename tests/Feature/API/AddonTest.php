<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\Addon;
use App\Models\Administrator;

class AddonTest extends TestCase
{
    //? success
    public function testIndexSuccess()
    {
        $response = $this->getJson('/api/addons');

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

        $addonData = [
            'name' => 'Test Addon',
            'description' => 'Test Description',
            'price' => 10,
            'status' => '1',
            // 'image' => '',
            'category_id' => 1
        ];

        $response = $this->postJson('/api/addons/store', $addonData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Addon created successfully'
        ]);
    }

    //? success
    public function testShowSuccess()
    {
        $addon = Addon::factory()->create(['status' => '1']);

        $response = $this->getJson('/api/addons/show/' . $addon->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    //? success
    public function testUpdateSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $addon = Addon::factory()->create(['status' => '1']);

        $update_data = [
            'name' => 'Updated Addon Name',
            'description' => 'Updated description.',
            'price' => 20,
            'type' => 'vegetarian',
            'status' => '1',
            'category_id' => 1,
        ];

        $response = $this->postJson("/api/addons/update/{$addon->id}", $update_data);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Addon updated successfully',
        ]);
    }

    //? success
    public function testUpdateStatusSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $addon = Addon::factory()->create(['status' => '0']);

        $response = $this->putJson("/api/addons/active/{$addon->id}", ['status' => '1']);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Addon status updated successfully',
        ]);
    }

    //? success
    public function testDestroySuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $addon = Addon::factory()->create(['status' => '1']);

        $response = $this->deleteJson("/api/addons/{$addon->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Addon deleted successfully',
        ]);
    }
}
