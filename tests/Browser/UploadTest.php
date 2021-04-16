<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class UploadTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $uploadUrl;
    private $loginUrl;

    public function setUp(): void
    {
        parent::setUp();

        // Куда будем заходить.
        $this->uploadUrl = route('upload_form', null, false);
        $this->loginUrl = route('login', null, false);
    }

    /**
     * Убедимся, что для доступа на страницу требуется аутентификация.
     *
     * @throws Throwable
     */
    public function test_upload_page_authentication_required()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit($this->uploadUrl)
                ->assertPathIs($this->loginUrl)
                ->assertGuest();
        });
    }

    /**
     * Убедимся, что для аутентифицированного пользователя доступ на страницу есть.
     *
     * @throws Throwable
     */
    public function test_upload_page_rendered_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit($this->uploadUrl)
                ->assertPathIs($this->uploadUrl)
                ->assertSee(
                    __('Upload a photo')
                )
                ->assertSee(
                    __('Drop image files here to upload or :link.', [
                        'link' => __('select them on your device')
                    ])
                );
        });
    }
}
