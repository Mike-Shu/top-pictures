<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    private $dashboardUrl;
    private $loginUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Куда будем заходить.
        $this->dashboardUrl = route('dashboard');
        $this->loginUrl = route('login');
    }

    /**
     * Страница "админка" должна быть доступна только для пользователя,
     * гость должен быть отправлен на авторизацию.
     */
    public function test_dashboard_screen_can_be_rendered()
    {
        // Для гостя.
        $this->assertGuest()
            ->get($this->dashboardUrl)
            ->assertRedirect($this->loginUrl);

        // Для пользователя.
        $this->actingAs($this->user)
            ->get($this->dashboardUrl)
            ->assertOk();
    }
}
