<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class DashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $dashboardUrl;
    private $loginUrl;

    public function setUp(): void
    {
        parent::setUp();

        // Куда будем заходить.
        $this->dashboardUrl = route('dashboard', null, false);
        $this->loginUrl = route('login', null, false);
    }

    /**
     * Убедимся, что для доступа на страницу требуется аутентификация.
     *
     * @throws Throwable
     */
    public function testDashboardPageAuthenticationRequired()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit($this->dashboardUrl)
                ->assertPathIs($this->loginUrl)
                ->assertGuest();
        });
    }

    /**
     * Убедимся, что для аутентифицированного пользователя доступ на страницу есть.
     *
     * @throws Throwable
     */
    public function testDashboardPageRenderedCorrectly()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit($this->dashboardUrl)
                ->assertSee('Dashboard page');
        });
    }
}
