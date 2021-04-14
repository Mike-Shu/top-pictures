<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

/**
 * Вспомогательный функционал для тестирования контроллеров.
 *
 * @package Http\Controllers
 */
class ControllersTestTools
{

    /**
     * Возвращает объект с ошибкой валидации.
     *
     * @param  string  $field    В каком поле должна вернуться ошибка?
     * @param  string  $message  С каким сообщением?
     *
     * @return ViewErrorBag
     */
    public static function getValidationError(string $field, string $message): ViewErrorBag
    {
        $messageBug = new MessageBag([
            $field => [
                $message
            ]
        ]);

        $result = new ViewErrorBag();
        $result->put('default', $messageBug);

        return $result;
    }

}