<?php

namespace App\Kernels\Http;

/**
 * リクエストに関する値を扱うクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class Request
{
    public function getRequestMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getPath()
    {
        $uri = $_SERVER['REQUEST_URI'];

        // パラメータの削除
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        return $uri;
    }

    /**
     * getパラメーターもしくは、リクエストボディの値を取得する。
     *
     * @return array $_GETもしくは$_POST
     */
    public function getAllParameters(): array
    {
        if($this->getRequestMethod() === 'get') {
            $plainParameters = $_GET;
        } else {
            $plainParameters = $_POST;
        }

        $convertParamaeterFnc = function ($para) {
            // 空白除去処理
            $trimmedPara = preg_replace('/\A[　 \n\r\t\v\x00]*|[　 \n\r\t\v\x00]*\z/u', '', $para);
            
            // 改行文字の統一
            return str_replace(["\r\n", "\r"], "\n", $trimmedPara);
        };

        return array_map($convertParamaeterFnc, $plainParameters);
    }
}
