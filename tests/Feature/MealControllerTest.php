<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use App\Http\Resources\ItemResource;
use Exception;

class MealControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_no_active_meals_message_when_no_meals_found()
    {
        // Empty the meals table to ensure there are no active meals
        Meal::truncate();

        $response = $this->getJson('/api/meals');

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'failed',
            'message' => 'No active meals found',
        ]);
    }

    /** @test */
    public function it_returns_active_meals_successfully()
    {
        // Create a meal with status '1'
        $meal = Meal::factory()->create(['status' => '1']);

        $response = $this->getJson('/api/meals');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'description' => $meal->description,
                    'price' => $meal->price,
                    'type' => $meal->type,
                    'status' => $meal->status,
                    'image' => $meal->image,
                    'category_id' => $meal->category_id,
                    'created_at' => $meal->created_at->toDateTimeString(),
                    'updated_at' => $meal->updated_at->toDateTimeString(),
                    // Assuming CategoryResource has a similar structure
                    'category' => [],
                ]
            ]
        ]);
    }

    /** @test */
    public function it_returns_internal_server_error_on_exception()
    {
        // Mock the Meal model to throw an exception
        $this->mock(Meal::class, function ($mock) {
            $mock->shouldReceive('with->where->get')->andThrow(new Exception('Test Exception'));
        });

        $response = $this->getJson('/api/meals');

        $response->assertStatus(500);
        $response->assertJson([
            'status' => 'failed',
            'message' => 'Internal server error',
            'error' => 'Test Exception',
        ]);
    }
}