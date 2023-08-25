<?php

namespace App\Kernels\Auth;

use App\Kernels\Configs\RouteAuthStatus;
use App\Kernels\Http\Response;
use App\Kernels\SessionManager;

use function App\Kernels\Utils\getAppConfig;

class Authentication
{
    public function __construct(
        private SessionManager $session
    ) {
    }

    /**
     * 現在の認証状態がルートの認証条件を満たしているか
     *
     * @param Response $response
     * @param RouteAuthStatus $routeStatus
     * @return void
     */
    public function handleRoute(Response $response, RouteAuthStatus $routeStatus): void
    {
        $isAuth = $this->isAuthenticated();

        switch ($routeStatus) {
            case RouteAuthStatus::Required:
                if (! $isAuth) {
                    // $response->redirect('/sign_in');
                }
                return;
            case RouteAuthStatus::UnAuthenticated:
                if ($isAuth) {
                    // $response->redirect('/');
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
        if ($this->session->hasSession()) {
            return $this->session->get(getAppConfig('sessionAuthKey')) !== null;
        }

        return false;
    }

    /**
     * 認証済みユーザーと同じユーザーであるか確認する
     *
     * @param string $authValue
     * @return boolean
     */
    public function isAuthenticatedUser(string $authValue): bool
    {
        $sessionAuthValue = (string) $this->session->get(getAppConfig('sessionAuthKey'));

        return $sessionAuthValue === $authValue;
    }
}
