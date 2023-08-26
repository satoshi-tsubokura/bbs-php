<?php

namespace App\Kernels\Securities;

use App\Kernels\SessionManager;

/**
 * CSRFトークンに関する処理を行うクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class CsrfHandler
{
    // CSRFトークンをセッションに保存する際の配列キー
    private const SESSION_KEY = 'csrf_token';

    public function __construct(
        private SessionManager $session,
    ) {
    }

    /**
     * csrfトークンを作成する
     *
     * @return string csrfトークン
     */
    public function create(): string
    {
        $token_bytes = 32;
        $token = uniqid(bin2hex(random_bytes($token_bytes)), more_entropy: true);
        $this->session->start();
        $this->session->set(self::SESSION_KEY, $token);
        return $token;
    }

    /**
     * csrfトークン検証処理を行う
     *
     * @param string|null $targetToken
     * @return boolean csrfトークンが正しいかを検証する
     */
    public function verify(?string $targetToken): bool
    {
        $sessionToken = $this->session->get(self::SESSION_KEY);

        return isset($targetToken) && $targetToken === $sessionToken;
    }
}
