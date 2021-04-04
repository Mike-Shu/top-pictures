<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationScreenCanBeRendered()
    {

        $response = $this->get(
            route('register')
        );

        $response->assertOk();

    }

    public function testNewUsersCanRegister(): void
    {

        $response = $this->post(
            route('register', [
                'name'                  => 'Test User',
                'email'                 => 'test@example.com',
                'password'              => 'password',
                'password_confirmation' => 'password',
            ])
        );

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);

    }
}
