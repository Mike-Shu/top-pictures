<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Вспомогательный функционал для тестирования пользовательских глобальных помощников.
 */
class HelpersTestTools
{
    /**
     * Возвращает текст комментария для указанной талицы.
     * Вернёт "null", если у таблицы нет комментария.
     *
     * @param  string  $dbName
     * @param  string  $tableName
     *
     * @return string|null
     */
    public static function getTableComment(string $dbName, string $tableName): ?string
    {
        $db = trim($dbName);
        $table = trim($tableName);

        if (empty_one_of($db, $table)) {
            return null;
        }

        $sqlResult = DB::select("
            SELECT TABLE_COMMENT
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
        ", [
            $db,
            $table,
        ]);

        if (empty($sqlResult)) {
            return null;
        }

        $result = current($sqlResult);

        if (($result instanceof stdClass) == false) {
            return null;
        }

        if (empty($result->TABLE_COMMENT)) {
            return null;
        }

        return (string)$result->TABLE_COMMENT;
    }

}
