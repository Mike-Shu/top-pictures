<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()->create();

    }

    public function testConfirmPasswordScreenCanBeRendered(): void
    {

        $response = $this
            ->actingAs($this->user)
            ->get(
                route('password.confirm')
            );

        $response->assertOk();

    }

    public function testPasswordCanBeConfirmed(): void
    {

        $response = $this
            ->actingAs($this->user)
            ->post(
                route('password.confirm', [
                    'password' => 'password',
                ])
            );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

    }

    public function testPasswordIsNotConfirmedWithInvalidPassword(): void
    {

        $response = $this
            ->actingAs($this->user)
            ->post(
                route('password.confirm', [
                    'password' => 'wrong-password',
                ])
            );

        $response->assertSessionHasErrors();

    }
}
