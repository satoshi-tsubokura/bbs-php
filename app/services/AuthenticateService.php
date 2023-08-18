<?php

namespace App\Services;

use App\Models\Databases\Repositories\UserRepository;
use App\Models\Entities\UserEntity;
use App\Kernels\SessionManager;

class AuthenticateService
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    /**
     * ユーザー認証の結果を返す
     *
     * @param string $username
     * @param string $plainPassword
     * @return UserEntity|false 認証に失敗した場合、falseを返す
     */
    public function fetchAuthUser(string $username, string $plainPassword): UserEntity|false
    {
        $user = $this->userRepository->fetchUserByName($username);

        // ユーザー名に合致するユーザーが存在しなかった場合
        if (! $user) {
            return false;
        }

        // パスワード認証
        return password_verify($plainPassword, $user->getPassword()) ? $user : false;
    }

    public function authenticate(UserEntity $user): void
    {
        $session = new SessionManager();
        $session->set('user_id', $user->getId());
        $session->set('user_name', $user->getUserName());

        // セッション固定化攻撃対策
        $session->reset();
    }
}
