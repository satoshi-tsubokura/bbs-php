<?php

namespace App\Kernels\Http;

/**
 * レスポンス処理を行うクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
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
     * 指定したパスにリダイレクトする
     *
     * @param string $path
     * @return void
     */
    public function redirect(string $path = '/'): void
    {
        $this->statusCode = 301;
        header("Location: {$path}", $this->statusCode);
        exit();
    }
}
