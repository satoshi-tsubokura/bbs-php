<?php

namespace App\Services;

use App\Models\Databases\Repositories\UserRepository;
use App\Models\Entities\UserEntity;

/**
 * ユーザーに関するビジネスロジックを実装するクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class UserService
{
    // デフォルトハッシュアルゴリズム名
    private const HASH_ALGOLISM = PASSWORD_BCRYPT;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    /**
     * ユーザー登録処理
     *
     * @param string $name
     * @param string $email
     * @param string $plainPassword
     * @return UserEntity|false 登録したユーザー
     * @throws \PDOException
     */
    public function registerUser(string $name, string $email, string $plainPassword): UserEntity|false
    {
        // パスワードのハッシュ化
        $hashedPassword = password_hash($plainPassword, self::HASH_ALGOLISM);

        // ユーザー名とメールアドレスで一意性チェック
        if ($this->userRepository->existsNameOrPassword($name, $email)) {
            return false;
        }

        // ユーザー登録処理
        $userEntity = new UserEntity(id: null, userName: $name, email: $email, password: $hashedPassword);
        $lastInsertId = $this->userRepository->insert($userEntity);

        if ($lastInsertId === false) {
            throw new \PDOException('ユーザーの登録に失敗しました。');
        }

        return $this->userRepository->fetchUserById($lastInsertId);
    }
}
