<?php

namespace App\Kernels;

use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\Validator;
use App\Kernels\AppLogger;

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
        $validator = new Validator($this->validatorRules, $parameters);
        return $validator->validate();
    }
}
