<?php

namespace App\Services;

use App\Models\Databases\Repositories\UserRepository;
use App\Models\Entities\UserEntity;
use App\Kernels\SessionManager;

use function App\Kernels\Utils\getAppConfig;

/**
 * 認証サービスクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class AuthenticateService
{
    /**
     * @param UserRepository $userRepository
     */
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

    /**
     * 認証処理を行う
     *
     * @param UserEntity $user
     * @return void
     */
    public function authenticate(UserEntity $user): void
    {
        $session = new SessionManager();
        $session->set(getAppConfig('sessionAuthKey'), $user->getId());
        $session->set(getAppConfig('sessionUserNameKey'), $user->getUserName());

        // セッション固定化攻撃対策
        $session->reset();
    }
}
