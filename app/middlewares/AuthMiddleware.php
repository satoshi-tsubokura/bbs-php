<?php

namespace App\Middlewares;

use App\Config\RouteAuthStatus;
use App\Utils\AuthUtil;

class AuthMiddleware
{
    /**
     * 現在の認証状態がルートの認証条件を満たしているか
     */
    public static function handleRoute(Response $response, RouteAuthStatus $routeStatus): void
    {
        $authUtil = new AuthUtil();
        $isAuth = $authUtil->isAuthenticated();

        switch ($routeStatus) {
            case RouteAuthStatus::Required:
                if (! $isAuth) {
                    $response->redirect('/sign_in');
                }
                return;
            case RouteAuthStatus::UnAuthenticated:
                if ($isAuth) {
                    $response->redirect('/');
                    exit;
                }
                return;
            case RouteAuthStatus::Optional:
                // 認証任意のルートに関しては何もしない
                return;
        }
    }
}
