<?php

namespace App\Middlewares;

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
}
