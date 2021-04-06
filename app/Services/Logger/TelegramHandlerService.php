<?php

namespace App\Services\Logger;

use App\Exceptions\TelegramLoggerException;
use Illuminate\Support\Facades\View;
use Log;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class TelegramHandlerService extends AbstractProcessingHandler
{

    const ERR_CODE_INVALID_ARGUMENT = 204; // Обязательный параметр является пустым.
    const ERR_CODE_WRONG_TEMPLATE = 404; // Указан ошибочный шаблон.

    /**
     * @var array
     */
    protected $config;

    /**
     * @var int
     */
    protected $timeout = 1;

    /**
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $level = Logger::toMonologLevel($this->config['level']);

        parent::__construct($level, true);
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     * @see
     */
    protected function write(array $record): void
    {
        try {

            $textChunks = $this->getFormattedChunks($record);
            $this->sendChunks($textChunks);

        } catch (TelegramLoggerException $e) {

            Log::channel('single')
                ->error(
                    $e->getCode().': '.$e->getMessage()
                );

        }
    }

    /**
     * @param  array  $record
     *
     * @return array
     * @throws TelegramLoggerException
     */
    protected function getFormattedChunks(array $record): array
    {

        return mb_str_split(
            $this->formatText($record),
            4096
        );

    }

    /**
     * @param  array  $chunks
     *
     * @throws TelegramLoggerException
     */
    protected function sendChunks(array $chunks): void
    {

        foreach ($chunks as $_textChunk) {
            $this->sendMessage($_textChunk);
        }

    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter("%message%");
    }

    /**
     * @param  array  $record
     *
     * @return string
     * @throws TelegramLoggerException
     */
    protected function formatText(array $record): string
    {
        $template = trim($this->config['template']);
        $template = "logger.telegram.{$template}";

        if (View::exists($template) === false) {
            throw new TelegramLoggerException(
                'Template is not defined for Telegram logger',
                static::ERR_CODE_WRONG_TEMPLATE
            );
        }

        $data = array_merge($record, [
            'app_name' => $this->config['app_name'],
            'app_env'  => config('app.env'),
        ]);

        return view($template, $data);
    }

    /**
     * @param  string  $text
     *
     * @throws TelegramLoggerException
     */
    protected function sendMessage(string $text): void
    {

        $url = $this->config['api_url'];
        $token = $this->config['bot_token'];
        $chatId = $this->config['chat_id'];

        if (empty($url)) {

            throw new TelegramLoggerException(
                'API url is not defined for Telegram logger',
                static::ERR_CODE_INVALID_ARGUMENT
            );

        }

        if (empty($token)) {

            throw new TelegramLoggerException(
                'Bot token is not defined for Telegram logger',
                static::ERR_CODE_INVALID_ARGUMENT
            );

        }

        if (empty($chatId)) {

            throw new TelegramLoggerException(
                'Bot chat id is not defined for Telegram logger',
                static::ERR_CODE_INVALID_ARGUMENT
            );

        }

        $url = preg_replace_array('/:[a-z_]+/', [
            $token,
        ], $url);

        $data = [
            'text'       => trim($text),
            'chat_id'    => $chatId,
            'parse_mode' => 'html',
        ];

        $headers = [
            'Accept: application/json',
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        $response = $this->getResponse($ch);

        curl_close($ch);

        if ($response['ok'] === false && isset($response['description'])) {

            throw new TelegramLoggerException(
                __METHOD__.': '.$response['description'],
                $response['error_code']
            );

        }

    }

    /**
     * @param  null  $ch
     *
     * @return mixed
     */
    protected function getResponse($ch = null)
    {

        return json_decode(
            (string)curl_exec($ch),
            true
        );

    }

}
