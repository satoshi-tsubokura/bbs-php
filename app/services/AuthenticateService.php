<?php

namespace App\Services;

use App\Databases\Repositories\UserRepository;
use App\Models\Entities\UserEntity;

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
     * @return UserEntity|false 認証に失敗した場合、nullを返す
     */
    public function authenticate(string $username, string $plainPassword): UserEntity|false
    {
        $user = $this->userRepository->searchUser($username);

        // ユーザー名に合致するユーザーが存在しなかった場合
        if (is_null($user)) {
            return false;
        }

        // パスワード認証
        return password_verify($plainPassword, $user->getPassword()) ? $user : false;
    }

    public function generateJwt(int $userId)
    {

    }
}
