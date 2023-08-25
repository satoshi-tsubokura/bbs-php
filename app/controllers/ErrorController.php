<?php

namespace App\Controllers;

use App\Kernels\AbstractController;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;

class ErrorController extends AbstractController
{
    public function error(int $statusCode = null): void
    {
        switch($statusCode) {
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
