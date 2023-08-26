<?php

namespace App\Controllers;

use App\Kernels\AbstractController;

/**
 * エラー画面表示に関する処理をするコントローラー
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class ErrorController extends AbstractController
{
    /**
     * エラー画面表示、ステータスコードによりメッセージ作成処理
     *
     * @param integer $statusCode HTTPステータスコード
     * @return void
     */
    public function error(int $statusCode = 500): void
    {
        switch($statusCode) {
            case 403:
                $errorMsg = '403 Forbidden';
                $errorDescription = '許可していない操作が確認されました';
                break;
            case 404:
                $errorMsg = '404 NOT FOUND';
                $errorDescription = 'ご指定のページが見つかりませんでした。';
                break;
            case 405:
                $errorMsg = '405 METHOD NOT ALLOWED';
                $errorDescription = '許可されたアクセス方法ではありません。';
                break;
            default:
                break;
        }

        require_once __DIR__ . '/../views/pages/error.php';
    }
}
