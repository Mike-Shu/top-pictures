<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()->create();

    }

    public function testLoginScreenCanBeRendered(): void
    {

        $response = $this->get(
            route('login')
        );

        $response->assertOk();

    }

    public function testUsersCanAuthenticateUsingTheLoginScreen(): void
    {

        $response = $this->post(
            route('login', [
                'email'    => $this->user->email,
                'password' => 'password',
            ])
        );

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);

    }

    public function testUsersCanNotAuthenticateWithInvalidPassword(): void
    {

        $this->post(
            route('login', [
                'email'    => $this->user->email,
                'password' => 'wrong-password',
            ])
        );

        $this->assertGuest();

    }
}
