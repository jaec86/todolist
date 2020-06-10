<?php

namespace Tests\Feature;

use App\Todo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TodosCreateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    protected function createTodo($data = [])
    {
        return $this->actingAs($this->user)
            ->postJson(route('todos.create'), $data);
    }

    /** @test */
    public function title_required()
    {
        $this->createTodo()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['title' => ['title_required']]
            ]);
    }

    /** @test */
    public function title_not_string()
    {
        $this->createTodo(['title' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['title' => ['title_not_string']]
            ]);   
    }

    /** @test */
    public function title_too_long()
    {
        $this->createTodo(['title' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['title' => ['title_too_long']]
            ]);   
    }

    /** @test */
    public function description_not_string()
    {
        $this->createTodo(['description' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['description' => ['description_not_string']]
            ]);   
    }

    /** @test */
    public function description_too_long()
    {
        $this->createTodo(['description' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['description' => ['description_too_long']]
            ]);   
    }

    /** @test */
    public function tags_not_array()
    {
        $this->createTodo(['tags' => 1])
            ->assertStatus(422)
            ->AssertJson([
                'message' => 'validation_error',
                'errors' => ['tags' => ['tags_not_array']]
            ]);
    }

    /** @test */
    public function todo_created()
    {
        $todo = factory(Todo::class)->make()->only(['title', 'description']);

        $response = $this->createTodo($todo)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'todo_created',
                'todo' => $todo
            ]);

        $this->assertDatabaseHas('todos', array_merge(['user_id' => $this->user->id], $todo));
    }
}
