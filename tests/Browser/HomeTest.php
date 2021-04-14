<?php

namespace Tests\Browser;

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
    public function test_homepage_rendered_correctly()
    {
        $message = __('Home page');

        $this->browse(function (Browser $browser) use ($message) {
            $browser->assertGuest()
                ->visit($this->homeUrl)
                ->assertPathIs($this->homeUrl)
                ->assertSee($message);
        });

        $this->browse(function (Browser $browser) use ($message) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit($this->homeUrl)
                ->assertPathIs($this->homeUrl)
                ->assertSee($message);
        });
    }
}
