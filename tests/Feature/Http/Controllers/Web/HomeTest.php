<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    private $homeUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Куда будем заходить.
        $this->homeUrl = route('home');
    }

    /**
     * Страница "галерея" должна быть доступна и для гостя, и для пользователя.
     */
    public function test_home_screen_can_be_rendered()
    {
        // Для гостя.
        $this->assertGuest()
            ->get($this->homeUrl)
            ->assertOk();

        // Для пользователя.
        $this->actingAs($this->user)
            ->get($this->homeUrl)
            ->assertOk();
    }
}
