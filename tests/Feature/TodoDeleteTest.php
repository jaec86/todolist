<?php

namespace Tests\Feature;

use App\Todo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->todo = factory(Todo::class)->create(['user_id' => $this->user->id]);
    }

    protected function deleteTodo($data = [])
    {
        return $this->actingAs($this->user)
            ->deleteJson(route('todos.delete', ['todo' => $this->todo->id]), $data);
    }

    /** @test */
    public function todo_not_found()
    {
        $this->todo->delete();

        $this->deleteTodo()
            ->assertStatus(404)
            ->assertJson(['message' => 'todo_not_found']);
    }

    /** @test */
    public function todo_exists_but_belongs_to_different_user()
    {
        $this->todo = factory(Todo::class)->create();

        $this->deleteTodo()
            ->assertStatus(404)
            ->assertJson(['message' => 'todo_not_found']);
    }

    /** @test */
    public function todo_delete()
    {
        $this->deleteTodo()
            ->assertStatus(200)
            ->assertJson(['message' => 'todo_deleted']);

        $this->assertDatabaseMissing('todos', $this->todo->only(['id', 'title']));   
    }
}
