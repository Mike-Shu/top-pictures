<?php


namespace App\Services;

/**
 * Общие вспомогательные инструменты для всех сервисов.
 *
 * @package App\Services
 */
class CommonTools
{

    /**
     * Возвращает строку с информацией о методе, который вызвал текущий метод: "{$method}() in {$file}:{$line}".
     * Используется для логирования ошибок.
     *
     * @param  int  $depth  Глубина вложенности текущего метода относительно метода, о котором нужна информация.
     *
     * @return string
     */
    public static function getCaller(int $depth = 1): string
    {
        $result = 'the caller is unknown';

        $backtrace = debug_backtrace(false);
        $caller = $backtrace[$depth];

        if (!empty($caller)) {

            $method = $caller['function'];
            $file = $caller['file'];
            $line = $caller['line'];

            $result = "{$method}() in {$file}:{$line}";

        }

        return $result;

    }

}