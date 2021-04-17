<?php

use Illuminate\Contracts\Cache\Repository;

if (!function_exists('empty_one_of')) {

    /**
     * Вернет "true", если хотя бы один из переданных аргументов НЕ является пустым.
     * Вернёт "false", если все аргументы пустые.
     * Проверяет все аргументы функцией empty().
     *
     * @return bool
     * @see empty()
     */
    function empty_one_of(): bool
    {
        $args = func_get_args();

        if (!empty($args)) {

            foreach ($args as $_arg) {

                if (empty($_arg)) {
                    return true;
                }

            }

        }

        return false;
    }

}

if (!function_exists('empty_all_of')) {

    /**
     * Вернёт "true", если все переданные аргументы являются пустыми.
     * Вернёт "false", если хотя бы один аргумент НЕ пустой.
     * Проверяет все аргументы функцией empty().
     *
     * @return bool
     * @see empty()
     */
    function empty_all_of(): bool
    {
        $args = func_get_args();

        if (!empty($args)) {

            foreach ($args as $_arg) {

                if (!empty($_arg)) {
                    return false;
                }

            }

        }

        return true;
    }

}

if (!function_exists('not_empty')) {

    /**
     * Вернёт "true", если все переданные аргументы НЕ пустые.
     * Вернёт "false", если хотя бы один аргумент пустой.
     * Проверяет все аргументы функцией empty().
     *
     * @return bool
     * @see empty()
     */
    function not_empty(): bool
    {
        $args = func_get_args();

        if (!empty($args)) {

            foreach ($args as $_arg) {
                if (empty($_arg)) {
                    return false;
                }
            }

        }

        return true;
    }

}

if (!function_exists('get_random_color')) {

    /**
     * Возвращает случайный цвет в HEX-кодировке (например: "#6C1DEF").
     *
     * @return string
     * @codeCoverageIgnore
     */
    function get_random_color(): string
    {
        return sprintf(
            '#%06X',
            mt_rand(0, 0xFFFFFF)
        );
    }

}

if (!function_exists('set_table_comment')) {

    /**
     * Добавляет комментарий для указанной таблицы.
     *
     * @param  string  $table  Имя таблицы, должно быть БЕЗ префикса.
     * @param  string  $comment
     */
    function set_table_comment(string $table, string $comment): void
    {
        $table = trim($table);
        $comment = trim($comment);

        if (not_empty($table, $comment)) {

            $prefix = config('database.connections.mysql.prefix');
            $tableName = $prefix.$table;

            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE `{$tableName}` comment '{$comment}'"
            );

        }
    }

}

if (!function_exists('runtime_cache')) {

    /**
     * Кеширование значений только на время выполнения скрипта. Пример использования:
     * ```
     * $cacheKey = md5('some string');
     * runtime_cache($cacheKey, 'some data');
     * // ...
     * $someData = runtime_cache($cacheKey);
     * ```
     *
     * @return bool|Repository|mixed
     */
    function runtime_cache()
    {
        $arguments = func_get_args();

        if (empty($arguments)) {
            return Cache::store('array');
        }

        if (count($arguments) == 1 && is_string($arguments[0])) {
            return Cache::store('array')->get(...$arguments);
        }

        return Cache::store('array')->put(...$arguments);
    }

}

if (!function_exists('dec2hex')) {

    /**
     * Возвращает полное шестнадцатеричное представление цвета, например:
     *  - 0        => #000000;
     *  - 128      => #000080;
     *  - 16777215 => #ffffff.
     *
     * @param  int   $num
     * @param  bool  $sharp
     *
     * @return string
     */
    function dec2hex(int $num, bool $sharp = true): string
    {
        $hex = dechex(abs($num));

        $prefix = $sharp ? '#' : '';
        $value = substr('000000'.$hex, -6);

        return $prefix.$value;
    }

}
