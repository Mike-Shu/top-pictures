<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()
            ->unverified()
            ->create();

    }

    public function testEmailVerificationScreenCanBeRendered(): void
    {

        $response = $this
            ->actingAs($this->user)
            ->get(
                route('verification.notice')
            );

        $response->assertOk();

    }

    public function testEmailCanBeVerified(): void
    {

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $this->user->id,
                'hash' => sha1($this->user->email),
            ]
        );

        $response = $this
            ->actingAs($this->user)
            ->get($verificationUrl);

        Event::assertDispatched(Verified::class);

        $this->assertTrue(
            $this->user
                ->fresh()
                ->hasVerifiedEmail()
        );

        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');

    }

    public function testEmailIsNotVerifiedWithInvalidHash(): void
    {

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $this->user->id,
                'hash' => sha1('wrong-email'),
            ]
        );

        $this
            ->actingAs($this->user)
            ->get($verificationUrl);

        $this->assertFalse(
            $this->user
                ->fresh()
                ->hasVerifiedEmail()
        );

    }
}
