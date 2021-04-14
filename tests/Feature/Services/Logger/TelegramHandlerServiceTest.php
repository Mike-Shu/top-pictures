<?php

namespace Tests\Feature\Services\Logger;

use App\Exceptions\TelegramLoggerException;
use App\Services\Logger\TelegramHandlerService;
use Monolog\Logger;
use Tests\TestCase;

class TelegramHandlerServiceTest extends TestCase
{
    private $config;
    private $message = "PHPUnit test message";

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = config('logging.channels.telegram');
    }

    /**
     * Проверяем "Template is not defined for Telegram logger".
     */
    public function testWrongTemplateException()
    {
        $this->config['template'] = 'wrong';
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionCode(TelegramHandlerService::ERR_CODE_WRONG_TEMPLATE);
        $this->brokenLogger()->critical($this->message);
    }

    /**
     * Проверяем "API url is not defined for Telegram logger".
     */
    public function testEmptyApiUrlException()
    {
        $this->config['api_url'] = '';
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionCode(TelegramHandlerService::ERR_CODE_INVALID_ARGUMENT);
        $this->brokenLogger()->critical($this->message);
    }

    /**
     * Проверяем "Bot token is not defined for Telegram logger".
     */
    public function testEmptyBotTokenException()
    {
        $this->config['bot_token'] = '';
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionCode(TelegramHandlerService::ERR_CODE_INVALID_ARGUMENT);
        $this->brokenLogger()->critical($this->message);
    }

    /**
     * Проверяем "Bot chat id is not defined for Telegram logger".
     */
    public function testEmptyBotChatIdException()
    {
        $this->config['chat_id'] = '';
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionCode(TelegramHandlerService::ERR_CODE_INVALID_ARGUMENT);
        $this->brokenLogger()->critical($this->message);
    }

    /**
     * Проверяем актуальность Telegram API.
     */
    public function testWrongApiUrlException()
    {
        $this->config['api_url'] = 'https://api.telegram.org/bot:token/wrongMethod';
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionCode(404);
        $this->brokenLogger()->critical($this->message);
    }

    /**
     * Успешная отправка сообщения.
     */
    public function testSuccessMessageSend()
    {
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionMessage('Ok');
        $this->expectExceptionCode(200);
        $this->workingLogger()->critical($this->message);
    }

    /**
     * Проверяем прочие ошибки, которые может вернуть Telegram API.
     */
    public function testInvalidBotTokenException()
    {
        $this->config['bot_token'] = '1711612193:WRONG-TOKEN';
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionCode(401);
        $this->brokenLogger()->critical($this->message);
    }

    public function testInvalidChatIdException()
    {
        $this->config['chat_id'] = '542395575';
        $this->expectException(TelegramLoggerException::class);
        $this->expectExceptionCode(400);
        $this->brokenLogger()->critical($this->message);
    }

    /**
     * Возвращает рабочий экземпляр.
     *
     * @return Logger
     */
    private function workingLogger(): Logger
    {
        return new Logger(
            config('app.name'),
            [
                new TelegramHandlerServiceSuccessStub($this->config),
            ]
        );
    }

    /**
     * Возвращает заведомо сломанный экземпляр.
     *
     * @return Logger
     */
    private function brokenLogger(): Logger
    {
        return new Logger(
            config('app.name'),
            [
                new TelegramHandlerServiceFailureStub($this->config),
            ]
        );
    }

}
