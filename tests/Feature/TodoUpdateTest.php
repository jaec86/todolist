<?php

namespace Tests\Feature;

use App\Todo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TodoUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->todo = factory(Todo::class)->create(['user_id' => $this->user->id]);
    }

    protected function updateTodo($data = [])
    {
        return $this->actingAs($this->user)
            ->putJson(route('todos.update', ['todo' => $this->todo->id]), $data);
    }

    /** @test */
    public function todo_not_found()
    {
        $this->todo->delete();

        $this->updatetodo()
            ->assertStatus(404)
            ->assertJson(['message' => 'todo_not_found']);
    }

    /** @test */
    public function todo_exists_but_belongs_to_different_user()
    {
        $this->todo = factory(Todo::class)->create();

        $this->updateTodo()
            ->assertStatus(404)
            ->assertJson(['message' => 'todo_not_found']);
    }

    /** @test */
    public function title_required()
    {
        $this->updateTodo()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['title' => ['title_required']]
            ]);
    }

    /** @test */
    public function title_not_string()
    {
        $this->updateTodo(['title' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['title' => ['title_not_string']]
            ]);   
    }

    /** @test */
    public function title_too_long()
    {
        $this->updateTodo(['title' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['title' => ['title_too_long']]
            ]);   
    }

    /** @test */
    public function description_not_string()
    {
        $this->updateTodo(['description' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['description' => ['description_not_string']]
            ]);   
    }

    /** @test */
    public function description_too_long()
    {
        $this->updateTodo(['description' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['description' => ['description_too_long']]
            ]);   
    }

    /** @test */
    public function tags_not_array()
    {
        $this->updateTodo(['tags' => 1])
            ->assertStatus(422)
            ->AssertJson([
                'message' => 'validation_error',
                'errors' => ['tags' => ['tags_not_array']]
            ]);
    }

    /** @test */
    public function priority_required()
    {
        $this->updateTodo()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['priority' => ['priority_required']]
            ]);
    }

    /** @test */
    public function priority_not_numeric()
    {
        $this->updateTodo(['priority' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['priority' => ['priority_not_numeric']]
            ]);   
    }

    /** @test */
    public function priority_invalid()
    {
        $this->updateTodo(['priority' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['priority' => ['priority_invalid']]
            ]);   
    }

    /** @test */
    public function todo_updated()
    {
        $todo = factory(Todo::class)->make()->only(['title', 'description', 'priority']);

        $this->updateTodo($todo)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'todo_updated',
                'todo' => $todo
            ]);

        $this->assertDatabaseHas('todos', array_merge(['user_id' => $this->user->id], $todo));
    }
}
