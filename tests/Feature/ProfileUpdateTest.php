<?php

namespace Tests\Feature;

use App\Notifications\VerifyEmail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
    }

    protected function updateProfile($data = [])
    {
        return $this->actingAs($this->user)
            ->putJson(route('profile.update'), $data);
    }

    /** @test */
    public function first_name_required()
    {
        $this->updateProfile()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['first_name' => ['first_name_required']]
            ]);
    }

    /** @test */
    public function first_name_not_string()
    {
        $this->updateProfile(['first_name' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['first_name' => ['first_name_not_string']]
            ]);   
    }

    /** @test */
    public function first_name_too_long()
    {
        $this->updateProfile(['first_name' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['first_name' => ['first_name_too_long']]
            ]);   
    }

    /** @test */
    public function last_name_required()
    {
        $this->updateProfile()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['last_name' => ['last_name_required']]
            ]);
    }

    /** @test */
    public function last_name_not_string()
    {
        $this->updateProfile(['last_name' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['last_name' => ['last_name_not_string']]
            ]);   
    }

    /** @test */
    public function last_name_too_long()
    {
        $this->updateProfile(['last_name' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['last_name' => ['last_name_too_long']]
            ]);   
    }

    /** @test */
    public function email_required()
    {
        $this->updateProfile()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_required']]
            ]);
    }

    /** @test */
    public function email_not_string()
    {
        $this->updateProfile(['email' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_not_string']]
            ]);
    }

    /** @test */
    public function email_invalid_email_address()
    {
        $this->updateProfile(['email' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_invalid_email_address']]
            ]);
    }

    /** @test */
    public function email_too_long()
    {
        $this->updateProfile(['email' => Str::random(250) . '@email.com'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_too_long']]
            ]);   
    }

    /** @test */
    public function email_taken()
    {
        $user = factory(User::class)->create();

        $this->updateProfile(['email' => $user->email])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_taken']]
            ]);
    }

    /** @test */
    public function profile_updated_with_same_data()
    {
        $user = $this->user->only(['id', 'first_name', 'last_name', 'email']);

        $this->updateProfile($user)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'profile_updated',
                'user' => $user
            ]);

        $this->assertDatabaseHas('users', $user);
    }

    /** @test */
    public function profile_updated()
    {
        Notification::fake();

        $user = factory(User::class)->make()->only(['first_name', 'last_name', 'email']);

        $this->updateProfile($user)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'profile_updated',
                'user' => $user
            ]);

        $this->assertDatabaseHas('users', array_merge(['email_verified_at' => null], $user));

        Notification::assertSentTo($this->user, VerifyEmail::class);
    }
}
