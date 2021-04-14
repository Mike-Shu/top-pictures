<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class CategoriesCreate extends DuskTestCase
{
    use DatabaseMigrations;

    private $createUrl;
    private $loginUrl;

    public function setUp(): void
    {
        parent::setUp();

        // Куда будем заходить.
        $this->createUrl = route('categories.create', null, false);
        $this->loginUrl = route('login', null, false);
    }

    /**
     * Убедимся, что для доступа на страницу требуется аутентификация.
     *
     * @throws Throwable
     */
    public function test_create_page_authentication_required()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->visit($this->createUrl)
                ->assertPathIs($this->loginUrl)
                ->assertGuest();
        });
    }

    /**
     * Убедимся, что для аутентифицированного пользователя доступ на страницу есть.
     *
     * @throws Throwable
     */
    public function test_create_page_rendered_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->assertGuest()
                ->loginAs($this->user)
                ->assertAuthenticatedAs($this->user)
                ->visit($this->createUrl)
                ->assertPathIs($this->createUrl)
                ->assertSee(
                    __('Add category')
                )
                ->assertSee(
                    __('Category name')
                )
                ->assertSee(
                    __('Category description')
                );
        });
    }
}
