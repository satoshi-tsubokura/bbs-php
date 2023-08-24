<?php

namespace App\Kernels\Auth;

use App\Kernels\Configs\RouteAuthStatus;
use App\Kernels\Http\Response;
use App\Kernels\SessionManager;

use function App\Kernels\Utils\getAppConfig;

class Authentication
{
    /**
     * 現在の認証状態がルートの認証条件を満たしているか
     */
    public function handleRoute(Response $response, RouteAuthStatus $routeStatus): void
    {
        $isAuth = $this->isAuthenticated();

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

    /**
     *  認証済みか否かを返すメソッド
     *
     * @return boolean 認証済みであるか
     */
    public function isAuthenticated(): bool
    {
        $session = new SessionManager();
        if ($session->hasSession()) {
            return $session->get(getAppConfig('sessionAuthKey')) !== null;
        }

        return false;
    }
}
