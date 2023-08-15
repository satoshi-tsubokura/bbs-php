<?php

namespace App\Utils;

class AuthUtil
{
    /**
     *  認証済みか否かを返すユーティリティメソッド
     *
     * @return boolean 認証済みであるか
     */
    public function isAuthenticated(): bool
    {
        $session = new SessionManager();
        if ($session->hasSession()) {
            $session->start();

            return $session->get('user_id') !== null;
        }

        return false;
    }
}
