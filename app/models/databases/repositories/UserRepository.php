<?php

namespace App\Models\Databases\Repositories;

use App\Models\Entities\UserEntity;

/**
 * USERSテーブルを操作するリポジトリクラス
 */
class UserRepository extends AbstractMysqlRepository
{
    /**
     * @override
     * @return void
     */
    protected function init(): void
    {
        $this->tableName = 'USERS';
    }

    /**
     * ユーザー名かメールアドレスが重複していないか
     *
     * @param string $name
     * @param string $email
     * @return boolean ユーザーが存在すればtrueで存在しなければfalse
     * @throws PDOException
     */
    public function existsNameOrPassword(string $name, string $email): bool
    {
        $countCol = 'count';
        $sql = 'SELECT COUNT(*) AS ' . $countCol . ' FROM ' . $this->tableName . ' WHERE user_name=:user_name OR email=:email LIMIT 1';
        $parameters = [
            ':user_name' => $name,
            ':email' => $email
        ];

        $result = $this->dbConnection->fetchFirstResult($sql, $parameters);

        return $result[$countCol] !== 0;
    }

    /**
     * ユーザー登録
     *
     * @param UserEntity $user
     * @return boolean 実行が成功したかどうか
     * @throws PDOException
     */
    public function insert(UserEntity $user)
    {
        $sql = 'INSERT INTO ' . $this->tableName . '(user_name, email, password) VALUES(:user_name, :email, :password)';
        $parameters = [
          ':user_name' => $user->getUserName(),
          ':email' => $user->getEmail(),
          ':password' => $user->getPassword()
        ];

        $this->dbConnection->executeQuery($sql, $parameters);
    }
}
