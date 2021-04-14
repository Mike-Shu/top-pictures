<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class GalleryTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $galleryUrl;

    public function setUp(): void
    {
        parent::setUp();

        // Куда будем заходить.
        $this->galleryUrl = route('categories.index', null, false);
    }

    /**
     * @throws Throwable
     */
    public function testGalleryPageGuestRenderedCorrectly()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit($this->galleryUrl)
                ->assertPathIs($this->galleryUrl)
                ->assertSee('Gallery');
        });
    }

    /**
     * @throws Throwable
     */
    public function testGalleryPageAuthRenderedCorrectly()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit($this->galleryUrl)
                ->assertPathIs($this->galleryUrl)
                ->assertSee('Gallery');
        });
    }
}
