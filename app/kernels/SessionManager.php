<?php

namespace App\Kernels;

/**
 * セッションに関する処理をラッピング・簡潔にするためのクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class SessionManager
{
    /**
     * @return boolean セッション開始に成功したか否か
     */
    public function start(): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        return session_start();
    }

    /**
     * セッションIDを再生成する
     *
     * @return boolean 再生成に成功したか
     */
    public function reset(): bool
    {
        $this->start();
        return session_regenerate_id();
    }

    /**
     * セッションに値を格納する
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    /**
     * セッションの値を取得する
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? null;
    }

    /**
     * $_SESSIONの値を空にする
     * NOTE: このクラスでは完全にセッションを削除できません。
     *       完全に削除するためにはdestroy()メソッドを利用してください。
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /**
     * セッションIDがクッキーに存在しているか
     *
     * @return boolean セッション自体が存在しているか
     */
    public function hasSession(): bool
    {
        return isset($_COOKIE[session_name()]);
    }

    /**
     * セッションを削除する
     *
     * @return boolean セッションの削除が成功したか
     */
    public function destroy(): bool
    {
        $this->start();
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            $cookieParams = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
        }

        return session_destroy();
    }
}
