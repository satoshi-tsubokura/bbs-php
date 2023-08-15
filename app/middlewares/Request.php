<?php

namespace App\Middlewares;

require_once __DIR__ . '/../utils/stringUtils.php';

use function App\Utils\trimSpaceStr;

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

        return array_map(fn ($para) => trimSpaceStr($para), $plainParameters);
    }
}
