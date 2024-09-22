<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\Extra;
use App\Models\Administrator;

class ExtraTest extends TestCase
{
    //? success
    public function testIndexSuccess()
    {
        $response = $this->getJson('/api/extras');

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

        $extraData = [
            'name' => 'Test Extra',
            'description' => 'Test Description',
            'price' => 10,
            'status' => '1',
            // 'image' => '',
            'category_id' => 1
        ];

        $response = $this->postJson('/api/extras/store', $extraData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Extra created successfully'
        ]);
    }

    //? success
    public function testShowSuccess()
    {
        $extra = Extra::factory()->create(['status' => '1']);

        $response = $this->getJson('/api/extras/show/' . $extra->id);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    //? success
    public function testUpdateSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $extra = Extra::factory()->create(['status' => '1']);

        $update_data = [
            'name' => 'Updated Extra Name',
            'description' => 'Updated description.',
            'price' => 20,
            'type' => 'vegetarian',
            'status' => '1',
            'category_id' => 1,
        ];

        $response = $this->postJson("/api/extras/update/{$extra->id}", $update_data);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Extra updated successfully',
        ]);
    }

    //? success
    public function testUpdateStatusSuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $extra = Extra::factory()->create(['status' => '0']);

        $response = $this->putJson("/api/extras/active/{$extra->id}", ['status' => '1']);

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Extra status updated successfully',
        ]);
    }

    //? success
    public function testDestroySuccess()
    {
        $user = Administrator::factory()->create(['role' => 'admin']);
        $this->actingAs($user, 'admin-api');

        $extra = Extra::factory()->create(['status' => '1']);

        $response = $this->deleteJson("/api/extras/{$extra->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Extra deleted successfully',
        ]);
    }
}
