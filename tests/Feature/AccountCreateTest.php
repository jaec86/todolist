<?php

namespace Tests\Feature;

use App\Notifications\VerifyEmail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function createAccount($data = [])
    {
        return $this->postJson(route('account.create'), $data);
    }

    /** @test */
    public function user_already_logged_in()
    {
        $this->actingAs(factory(User::class)->create())
            ->createAccount()
            ->assertStatus(200)
            ->assertJson(['message' => 'user_already_logged_in']);
    }

    /** @test */
    public function first_name_required()
    {
        $this->createAccount()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['first_name' => ['first_name_required']]
            ]);
    }

    /** @test */
    public function first_name_not_string()
    {
        $this->createAccount(['first_name' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['first_name' => ['first_name_not_string']]
            ]);
    }

    /** @test */
    public function first_name_too_long()
    {
        $this->createAccount(['first_name' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['first_name' => ['first_name_too_long']]
            ]);   
    }

    /** @test */
    public function last_name_required()
    {
        $this->createAccount()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['last_name' => ['last_name_required']]
            ]);
    }

    /** @test */
    public function last_name_not_string()
    {
        $this->createAccount(['last_name' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['last_name' => ['last_name_not_string']]
            ]);
    }

    /** @test */
    public function last_name_too_long()
    {
        $this->createAccount(['last_name' => Str::random(256)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['last_name' => ['last_name_too_long']]
            ]);   
    }

    /** @test */
    public function email_required()
    {
        $this->createAccount()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_required']]
            ]);
    }

    /** @test */
    public function email_not_string()
    {
        $this->createAccount(['email' => 10])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_not_string']]
            ]);
    }

    /** @test */
    public function email_invalid_email_address()
    {
        $this->createAccount(['email' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_invalid_email_address']]
            ]);
    }

    /** @test */
    public function email_too_long()
    {
        $this->createAccount(['email' => Str::random(250) . '@email.com'])
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

        $this->createAccount(['email' => $user->email])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_taken']]
            ]);
    }

    /** @test */
    public function password_required()
    {
        $this->createAccount()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_required']]
            ]);
    }

    /** @test */
    public function password_not_confirmed()
    {
        $this->createAccount(['password' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_not_confirmed']]
            ]);
    }

    /** @test */
    public function password_too_short()
    {
        $this->createAccount(['password' => '1234567', 'password_confirmation' => '1234567'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_too_short']]
            ]);
    }

    /** @test */
    public function account_created()
    {
        Notification::fake();

        $user = factory(User::class)->make();
        $data = array_merge(['password' => '1234567890', 'password_confirmation' => '1234567890'], $user->only(['first_name', 'last_name', 'email']));

        $response = $this->createAccount($data)
            ->assertStatus(201)
            ->assertJson([
                'message' => 'user_logged_in',
                'user' => $user->only(['first_name', 'last_name', 'email'])
            ]);

        $this->assertDatabaseHas('users', $user->only(['first_name', 'last_name', 'email']));

        $user = User::where('email', $user->email)->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
