<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileUpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->user = factory(User::class)->create();
    }

    protected function updatePassword($data = [])
    {
        return $this->actingAs($this->user)
            ->putJson(route('profile.password'), $data);
    }

    /** @test */
    public function password_required()
    {
        $this->updatePassword()
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_required']]
            ]);
    }

    /** @test */
    public function password_not_confirmed()
    {
        $this->updatePassword(['password' => Str::random(10)])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_not_confirmed']]
            ]);
    }

    /** @test */
    public function password_too_short()
    {
        $this->updatePassword(['password' => '1234567', 'password_confirmation' => '1234567'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'validation_error',
                'errors' => ['password' => ['password_too_short']]
            ]);
    }

    /** @test */
    public function password_updated()
    {
        $password = Str::random(10);

        $response = $this->updatePassword(['password' => $password, 'password_confirmation' => $password])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'password_updated',
                'user' => $this->user->only(['id', 'email'])
            ]);

        $this->assertTrue(Hash::check($password, $this->user->password));
    }
}
