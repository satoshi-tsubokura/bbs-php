<?php

namespace App\Middlewares;

use App\Utils\SessionManager;

class CsrfMiddleware
{
    private SessionManager $session;
    private const SESSION_KEY = 'csrf_token';

    public function __construct()
    {
        $this->session = new SessionManager();
    }
    public function create(): string
    {
        $token_bytes = 32;
        $token = uniqid(bin2hex(random_bytes($token_bytes)), more_entropy: true);
        $this->session->start();
        $this->session->set(self::SESSION_KEY, $token);
        return $token;
    }

    public function verify(?string $targetToken): bool
    {
        $this->session->start();
        $sessionToken = $this->session->get(self::SESSION_KEY);

        return isset($targetToken) && $targetToken === $sessionToken;
    }
}
