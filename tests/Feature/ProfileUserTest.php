<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileUserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
    }

    protected function getUser()
    {
        return $this->actingAs($this->user)
            ->getJson(route('profile.user'));
    }

    /** @test */
    public function get_profile_user()
    {
        $this->getUser()
            ->assertStatus(200)
            ->assertJson([
                'message' => 'user_found',
                'user' => $this->user->only(['id', 'email'])
            ]);
    }
}
