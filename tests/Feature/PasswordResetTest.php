<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function reset($data = [])
    {
        return $this->postJson(route('password.reset'), $data);
    }

    /** @test */
    public function token_required()
    {
        $this->reset()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['token' => ['token_required']]
            ]);
    }

    /** @test */
    public function email_required()
    {
        $this->reset()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_required']]
            ]);
    }

    /** @test */
    public function email_invalid_email_address()
    {
        $this->reset(['email' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_invalid_email_address']]
            ]);
    }

    /** @test */
    public function password_required()
    {
        $this->reset()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_required']]
            ]);
    }

    /** @test */
    public function password_not_confirmed()
    {
        $this->reset(['password' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_not_confirmed']]
            ]);
    }

    /** @test */
    public function password_too_short()
    {
        $this->reset(['password' => '1234567', 'password_confirmation' => '1234567'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_too_short']]
            ]);
    }

    /** @test */
    public function email_not_registered()
    {
        $this->reset([
            'token' => 'whatever',
            'email' => 'johndoe@email.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_not_registered']]
            ]);
    }

    /** @test */
    public function reset_token_invalid()
    {
        $user = factory(User::class)->create();

        $this->reset([
            'token' => 'whatever',
            'email' => $user->email,
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        ])->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['reset_token_invalid']]
            ]);
    }

    /** @test */
    public function password_was_reset()
    {
        $user = factory(User::class)->create();

        $response = $this->reset([
            'token' => Password::broker('users')->createToken($user),
            'email' => $user->email,
            'password' => 'new_password',
            'password_confirmation' => 'new_password'
        ])->assertStatus(200)
            ->assertJson(['message' => 'password_was_reset']);
    }
}
