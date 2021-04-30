<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Класс предполагает работу с HTTP-запросом.
 *
 * @package App\Services
 */
class RequestService
{
    const STATUS_OK = "Ok"; // Запрос выполнен успешно.
    const STATUS_FAILED = "Failed"; // При выполнении запроса что-то пошло не так.

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}