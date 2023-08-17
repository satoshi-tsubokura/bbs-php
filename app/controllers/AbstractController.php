<?php

namespace App\Controllers;

use App\Middlewares\Request;
use App\Middlewares\Response;
use App\Middlewares\Validations\RequestValidator;
use App\Utils\AppLogger;

abstract class AbstractController
{
    protected AppLogger $logger;
    protected array $validatorRules;
    public function __construct(
        protected Request $request,
        protected Response $response
    ) {
        $this->logger = AppLogger::getInstance();
    }

    /**
     * リクエストパラメーターのバリデーション結果を返す
     *
     * @return array エラーメッセージ
     */
    protected function validate(array $parameters): array
    {
        $validator = new RequestValidator($this->validatorRules, $parameters);
        return $validator->validate();
    }
}
