<?php

namespace Tests\Feature;

use App\Todo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TodosIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    protected function getTodos($data = [])
    {
        return $this->actingAs($this->user)
            ->getJson(route('todos.index', $data));
    }

    /** @test */
    public function todos_listed()
    {
        factory(Todo::class, 25)->create(['user_id' => $this->user]);

        $response = $this->getTodos()
            ->assertStatus(200)
            ->assertJson([
                'message' => 'todos_listed',
                'todos' => ['total' => 25, 'current_page' => 1]
            ]);

        $this->assertTrue(count($response->json('todos.data')) === 20);
    }

    /** @test */
    public function contacts_listed_second_page()
    {
        factory(Todo::class, 25)->create(['user_id' => $this->user]);

        $response = $this->getTodos(['page' => 2])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'todos_listed',
                'todos' => ['total' => 25, 'current_page' => 2]
            ]);

        $this->assertTrue(count($response->json('todos.data')) === 5);
    }

    /** @test */
    public function contacts_listed_with_search()
    {
        $term = Str::random(5);
        factory(Todo::class, 5)->create(['user_id' => $this->user, 'title' => $this->faker->sentence . ' ' . $term]);
        factory(Todo::class, 3)->create(['user_id' => $this->user, 'description' => $this->faker->paragraph . ' ' . $term]);
        factory(Todo::class, 2)->create(['user_id' => $this->user, 'tags' => $term]);
        
        factory(Todo::class, 5)->create(['user_id' => $this->user]);

        $response = $this->getTodos(['search' => $term])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'todos_listed',
                'todos' => ['total' => 10]
            ]);

        $this->assertTrue(count($response->json('todos.data')) === 10);
    }
}
