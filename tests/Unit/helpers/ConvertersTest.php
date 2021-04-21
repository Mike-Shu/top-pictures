<?php

namespace Tests\Unit\helpers;

use App\Items\RgbColorItem;
use PHPUnit\Framework\TestCase;

class ConvertersTest extends TestCase
{
    /**
     * Тестируем функцию "dec2hex()".
     *
     * @return void
     */
    public function test_dec2hex()
    {
        $this->assertEquals(
            '#000000',
            dec2hex(0)
        );

        $this->assertEquals(
            '#000080',
            dec2hex(128)
        );

        $this->assertEquals(
            '#ffffff',
            dec2hex(16777215)
        );

        $this->assertEquals(
            '#ffffff',
            dec2hex(-16777215)
        );
    }

    /**
     * Тестируем функцию "hex2rgb()".
     */
    public function test_hex2rgb()
    {
        $this->assertEquals(
            [255, 128, 0],
            hex2rgb('#ff8000')
        );

        $this->assertEquals(
            [255, 128, 0],
            hex2rgb('ff8000')
        );

        $this->assertEquals(
            [0, 15, 255],
            hex2rgb('#fff') // Должно быть воспринято как "#000fff".
        );
    }

    /**
     * Тестируем функцию "rgb2hex()".
     */
    public function test_rgb2hex()
    {
        $rgb = new RgbColorItem([255, 128, 0]);

        $this->assertEquals(
            '#ff8000',
            rgb2hex($rgb)
        );

        $this->assertEquals(
            'ff8000',
            rgb2hex($rgb, false)
        );
    }
}
