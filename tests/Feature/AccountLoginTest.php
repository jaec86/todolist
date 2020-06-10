<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AccountLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function login($data = [])
    {
        return $this->postJson(route('account.login'), $data);
    }

    /** @test */
    public function user_already_logged_in()
    {
        $this->actingAs(factory(User::class)->create())
            ->login()
            ->assertStatus(200)
            ->assertJson(['message' => 'user_already_logged_in']);
    }

    /** @test */
    public function email_required()
    {
        $this->login()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_required']]
            ]);
    }

    /** @test */
    public function password_required()
    {
        $this->login()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_required']]
            ]);
    }

    /** @test */
    public function credentials_invalid()
    {
        $this->login([
            'email' => 'johndoe@email.com',
            'password' => 'password'
        ])->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['credentials_invalid']]
            ]);
    }

    /** @test */
    public function block_login_after_five_failed_attempts()
    {
        $data = [
            'email' => 'johndoe@email.com',
            'password' => 'secret'
        ];

        for ($i = 0; $i < 8; $i++) {
            $this->login($data);
        }

        $this->login($data)->assertStatus(429)
            ->assertJson(['errors' => ['email' => ['too_many_attempts']]]);
    }

    /** @test */
    public function user_logged_in()
    {
        $user = factory(User::class)->create();

        $this->login([
            'email' => $user->email,
            'password' => 'password'
        ])->assertStatus(200)
            ->assertJson([
                'message' => 'user_logged_in',
                'user' => $user->only(['id', 'first_name', 'last_name', 'email'])
            ]);

        $this->assertTrue(Auth::user()->id === $user->id);
    }
}
