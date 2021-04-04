<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{

    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()->create();

    }

    public function testDashboardPageCanBeRendered(): void
    {

        $response = $this
            ->actingAs($this->user)
            ->get(
                route('dashboard')
            );

        $response->assertOk();

    }
}
