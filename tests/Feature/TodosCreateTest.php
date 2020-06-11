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
    public function description_required()
    {
        $this->createTodo()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['description' => ['description_required']]
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
    public function tags_not_string()
    {
        $this->createTodo(['tags' => 1])
            ->assertStatus(422)
            ->AssertJson([
                'message' => 'validation_error',
                'errors' => ['tags' => ['tags_not_string']]
            ]);
    }

    /** @test */
    public function priority_required()
    {
        $this->createTodo()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['priority' => ['priority_required']]
            ]);
    }

    /** @test */
    public function priority_not_numeric()
    {
        $this->createTodo(['priority' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['priority' => ['priority_not_numeric']]
            ]);   
    }

    /** @test */
    public function priority_invalid()
    {
        $this->createTodo(['priority' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['priority' => ['priority_invalid']]
            ]);   
    }

    /** @test */
    public function done_required()
    {
        $this->createTodo()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['done' => ['done_required']]
            ]);
    }

    /** @test */
    public function done_not_boolean()
    {
        $this->createTodo(['done' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['done' => ['done_not_boolean']]
            ]);
    }

    /** @test */
    public function todo_created()
    {
        $todo = factory(Todo::class)->make()->only(['title', 'description', 'priority', 'done']);

        $response = $this->createTodo($todo)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'todo_created',
                'todo' => $todo
            ]);

        $this->assertDatabaseHas('todos', array_merge(['user_id' => $this->user->id], $todo));
    }
}
