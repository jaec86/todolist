<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create(['email_verified_at' => null]);
    }

    protected function verify($signedURL = null)
    {
        $signedURL = $signedURL ?: URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $this->user->id,
                'hash' => sha1($this->user->email),
            ]
        );

        return $this->postJson($signedURL);
    }

    /** @test */
    public function unauthenticated_user()
    {
        $this->verify()
            ->assertStatus(401)
            ->assertJson(['message' => 'unauthenticated']);
    }

    /** @test */
    public function signature_expired()
    {
        $signedURL = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->subMinutes(60),
            [
                'id' => $this->user->id,
                'hash' => sha1($this->user->email),
            ]
        );

        $this->actingAs($this->user)
            ->verify($signedURL)
            ->assertStatus(403)
            ->assertJson(['message' => 'invalid_signature']);
    }

    /** @test */
    public function email_already_verified()
    {
        $this->user = factory(User::class)->create();

        $this->actingAs($this->user)
            ->verify()
            ->assertStatus(200)
            ->assertJson(['message' => 'email_already_verified']);

        $this->assertDatabaseMissing('users', ['id' => $this->user->id, 'email_verified_at' => null]);
    }

    /** @test */
    public function email_verified()
    {
        $this->actingAs($this->user)
            ->verify()
            ->assertStatus(200)
            ->assertJson([
                'message' => 'email_verified',
                'user' => $this->user->only(['id', 'email'])
            ]);

        $this->assertDatabaseMissing('users', ['id' => $this->user->id, 'email_verified_at' => null]);
    }
}
