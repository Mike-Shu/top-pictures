<?php

namespace Tests\Feature;

use App\Items\RgbColorItem;
use Exception;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Psr\SimpleCache\InvalidArgumentException;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use RefreshDatabase;

    private $runtimeCacheValue;
    private $runtimeCacheKey;

    protected function setUp(): void
    {
        parent::setUp();

        $this->runtimeCacheValue = 'some value';
        $this->runtimeCacheKey = md5($this->runtimeCacheValue);

    }

    /**
     * Тестируем функцию "set_table_comment()".
     *
     * @return void
     */
    public function test_set_table_comment()
    {
        $table = 'aliens';
        $expectedComment = 'Инопланетяне';

        $db = config('database.connections.mysql_testing.database');
        $prefix = config('database.connections.mysql.prefix');
        $tableName = $prefix.$table;

        // Создадим новую таблицу.
        Schema::create($table, function (Blueprint $table) {
            $table->id();
        });

        // Убедимся, что у неё нет комментария.
        $actualComment = HelpersTestTools::getTableComment($db, $tableName);
        $this->assertNull($actualComment);

        // Добавим комментарий.
        set_table_comment($table, $expectedComment);

        // Убедимся, что комментарий успешно добавлен.
        $actualComment = HelpersTestTools::getTableComment($db, $tableName);
        $this->assertEquals($expectedComment, $actualComment);
    }

    /**
     * Тестируем функцию "runtime_cache()".
     */
    public function test_runtime_cache()
    {
        $this->assertTrue(
            runtime_cache(
                $this->runtimeCacheKey,
                $this->runtimeCacheValue
            )
        );

        $this->assertEquals(
            $this->runtimeCacheValue,
            runtime_cache($this->runtimeCacheKey)
        );
    }

    /**
     * Функция "runtime_cache()" должна хранить значение только на время выполнения скрипта.
     */
    public function test_runtime_cache_get_again()
    {
        $this->assertNull(
            runtime_cache($this->runtimeCacheKey)
        );

        $this->assertInstanceOf(Repository::class, runtime_cache());
    }

    /**
     * Тестируем функцию "rgb2reference_hex()".
     *
     * @return void
     */
    public function test_rgb2reference_hex()
    {
        $rgb = new RgbColorItem([1, 2, 3]);

        $this->assertEquals(
            '#000000',
            rgb2reference_hex($rgb)
        );

        $this->assertEquals(
            '000000',
            rgb2reference_hex($rgb, false)
        );
    }
}
