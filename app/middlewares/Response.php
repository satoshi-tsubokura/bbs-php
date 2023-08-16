<?php

namespace App\Middlewares;

class Response
{
    /**
     * 送信データとHTTPステータスコードを設定する
     *
     * @param array $responseData
     * @param integer $statusCode
     */
    public function __construct(
        protected array $responseData = [],
        protected int $statusCode = 200
    ) {
    }

    /**
     * JSON形式のデータを送信する
     *
     * @return void
     */
    public function send(): void
    {
        header('Content-Type: application/json');
        http_response_code($this->statusCode);
        print json_encode($this->responseData);
    }

    /**
     * 指定したパスにリダイレクトする
     *
     * @param string $path
     * @return void
     */
    public function redirect(string $path = '/'): void
    {
        header("Location: {$path}");
        exit();
    }
}
