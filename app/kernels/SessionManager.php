<?php

namespace App\Kernels;

class SessionManager
{
    public function start(): bool
    {
        return session_start();
    }

    public function reset(): bool
    {
        return session_regenerate_id();
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? null;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function hasSession(): bool
    {
        return isset($_COOKIE[session_name()]);
    }

    public function destroy(): bool
    {
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            $cookieParams = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
        }

        return session_destroy();
    }
}
