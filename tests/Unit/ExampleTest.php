<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{

    use WithFaker;

    private $user;

    public function setUp(): void
    {

        parent::setUp();

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $aa1 = 100500;

        $test = 100;

        $this->assertTrue(true);

    }
}
