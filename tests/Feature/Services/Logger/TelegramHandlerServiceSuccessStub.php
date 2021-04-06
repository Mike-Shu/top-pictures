<?php

namespace Tests\Feature\Services\Logger;

use App\Exceptions\TelegramLoggerException;

/**
 * Заглушка для тестирования успешной отправки сообщения.
 *
 * @package Tests\Feature\Services\Logger
 */
class TelegramHandlerServiceSuccessStub extends TelegramHandlerServiceFailureStub
{

    /**
     * Если метод отработает без ошибок, то по завершении выбросим специальное исключение, чтобы при тестировании
     * отловить его и однозначно понять, что запрос был выполнен успешно и сообщение ушло в Telegram.
     *
     * @param  array  $chunks
     *
     * @throws TelegramLoggerException
     */
    protected function sendChunks(array $chunks): void
    {
        parent::sendChunks($chunks);

        throw new TelegramLoggerException('Ok', 200);
    }

    /**
     * Имитация ответа от Telegram API об успешной отправке запроса.
     *
     * @param  null  $ch
     *
     * @return bool[]
     */
    protected function getResponse($ch = null): array
    {
        return [
            'ok' => true,
        ];
    }

}
