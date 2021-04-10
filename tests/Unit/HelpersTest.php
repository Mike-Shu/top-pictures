<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @return void
     */
    public function test_empty_one_of()
    {
        $this->assertTrue(
            empty_one_of('', 'bar', 'baz')
        );

        $this->assertTrue(
            empty_one_of('foo', 0, 'baz')
        );

        $this->assertTrue(
            empty_one_of('foo', 'bar', null)
        );

        $this->assertTrue(
            empty_one_of('', null, 0)
        );

        $this->assertFalse(
            empty_one_of('foo', 'bar', 'baz')
        );
    }

    /**
     * @return void
     */
    public function test_empty_all_of()
    {
        $this->assertTrue(
            empty_all_of('', null, 0)
        );

        $this->assertFalse(
            empty_all_of('', 'bar', 'baz')
        );

        $this->assertFalse(
            empty_all_of('foo', 0, 'baz')
        );

        $this->assertFalse(
            empty_all_of('foo', 'bar', null)
        );

        $this->assertFalse(
            empty_all_of('foo', 'bar', 'baz')
        );
    }

    /**
     * @return void
     */
    public function test_not_empty()
    {
        $this->assertTrue(
            not_empty('foo', 1, [1])
        );

        $this->assertFalse(
            not_empty('')
        );

        $this->assertFalse(
            not_empty(0)
        );

        $this->assertFalse(
            not_empty([])
        );

        $this->assertFalse(
            not_empty(null)
        );
    }
}
