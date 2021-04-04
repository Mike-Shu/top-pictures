<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()->create();

    }

    public function testResetPasswordLinkScreenCanBeRendered(): void
    {

        $response = $this->get(
            route('password.request')
        );

        $response->assertOk();

    }

    public function testResetPasswordLinkCanBeRequested(): void
    {

        Notification::fake();

        $this->post(
            route('password.email', [
                'email' => $this->user->email
            ])
        );

        Notification::assertSentTo(
            $this->user,
            ResetPassword::class
        );

    }

    public function testResetPasswordScreenCanBeRendered(): void
    {

        Notification::fake();

        $this->post(
            route('password.email', [
                'email' => $this->user->email
            ])
        );

        Notification::assertSentTo(
            $this->user,
            ResetPassword::class,
            function ($notification) {

                $response = $this->get(
                    route('password.reset', [
                        'token' => $notification->token
                    ])
                );

                $response->assertOk();

                return true;

            });

    }

    public function testPasswordCanBeResetWithValidToken(): void
    {

        Notification::fake();

        $this->post(
            route('password.email', [
                'email' => $this->user->email
            ])
        );

        Notification::assertSentTo(
            $this->user,
            ResetPassword::class,
            function ($notification) {

                $response = $this->post(
                    route('password.update', [
                        'token'                 => $notification->token,
                        'email'                 => $this->user->email,
                        'password'              => 'password',
                        'password_confirmation' => 'password',
                    ])
                );

                $response->assertSessionHasNoErrors();

                return true;

            });

    }
}
