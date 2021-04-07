<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class HomeTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $homeUrl;

    public function setUp(): void
    {
        parent::setUp();

        // Куда будем заходить.
        $this->homeUrl = route('home', null, false);
    }

    /**
     * @throws Throwable
     */
    public function testHomePageGuestRenderedCorrectly()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit($this->homeUrl)
                ->assertPathIs($this->homeUrl)
                ->assertSee('Home page');
        });
    }

    /**
     * @throws Throwable
     */
    public function testHomePageAuthRenderedCorrectly()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit($this->homeUrl)
                ->assertPathIs($this->homeUrl)
                ->assertSee('Home page');
        });
    }
}
