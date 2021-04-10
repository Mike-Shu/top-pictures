<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use RefreshDatabase;

    /**
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
}
