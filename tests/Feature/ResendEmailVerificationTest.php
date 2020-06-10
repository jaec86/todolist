<?php

namespace Tests\Feature;

use App\Notifications\VerifyEmail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResendEmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create(['email_verified_at' => null]);
    }

    protected function resend()
    {
        return $this->actingAs($this->user)
            ->postJson(route('verification.resend'));
    }

    /** @test */
    public function email_already_verified()
    {
        Notification::fake();

        $this->user = factory(User::class)->create();

        $this->resend()
            ->assertStatus(200)
            ->assertJson(['message' => 'email_already_verified']);

        $this->assertDatabaseMissing('users', ['id' => $this->user->id, 'email_verified_at' => null]);

        Notification::assertNothingSent();
    }

    /** @test */
    public function email_verification_sent()
    {
        Notification::fake();

        $this->resend()
            ->assertStatus(200)
            ->assertJson([
                'message' => 'email_verification_sent',
                'user' => $this->user->only(['id', 'email', 'email_verified_at'])
            ]);

        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'email_verified_at' => null]);

        Notification::assertSentTo($this->user, VerifyEmail::class);
    }
}
