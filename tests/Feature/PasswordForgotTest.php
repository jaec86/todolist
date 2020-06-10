<?php

namespace Tests\Feature;

use App\Notifications\ResetPassword;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordForgotTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function forgot($data = [])
    {
        return $this->postJson(route('password.forgot'), $data);
    }

    /** @test */
    public function email_required()
    {
        $this->forgot()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_required']]
            ]);
    }

    /** @test */
    public function email_invalid_email_address()
    {
        $this->forgot(['email' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_invalid_email_address']]
            ]);
    }

    /** @test */
    public function email_not_registered()
    {
        $this->forgot(['email' => $this->faker->email])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['email' => ['email_not_registered']]
            ]);
    }

    /** @test */
    public function reset_link_sent()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $this->forgot(['email' => $user->email])
            ->assertStatus(200)
            ->assertJson(['message' => 'reset_link_sent']);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
