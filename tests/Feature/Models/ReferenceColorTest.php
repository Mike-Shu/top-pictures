<?php

namespace Tests\Feature\Models;

use App\Models\ReferenceColor;
use Tests\TestCase;

class ReferenceColorTest extends TestCase
{
    /**
     * @return void
     */
    public function test_updates_are_not_allowed()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);

        /**
         * @var $color ReferenceColor
         */
        $color = ReferenceColor::find(1);
        $color->hex = get_random_color();
        $color->update();

    }

    /**
     * @return void
     */
    public function test_save_are_not_allowed()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);

        /**
         * @var $color ReferenceColor
         */
        $color = ReferenceColor::find(1);
        $color->hex = get_random_color();
        $color->save();

    }

    /**
     * @return void
     */
    public function test_delete_are_not_allowed()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(500);

        /**
         * @var $color ReferenceColor
         */
        $color = ReferenceColor::find(1);
        $color->delete();

    }
}
