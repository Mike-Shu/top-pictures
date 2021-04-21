<?php

namespace Tests\Unit;

use App\Services\CommonTools;
use PHPUnit\Framework\TestCase;

class ServicesCommonToolsTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_get_caller()
    {
        $caller = CommonTools::getCaller();
        $this->assertStringContainsString('test_get_caller()', $caller);

        $caller = CommonTools::getCaller(0);
        $this->assertStringContainsString('getCaller()', $caller);
    }
}
