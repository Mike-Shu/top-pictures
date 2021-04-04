<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{

    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()->create();

    }

    public function testHomePageCanBeRendered(): void
    {

        $response = $this->get(
            route('home')
        );

        $response->assertOk();

    }
}
