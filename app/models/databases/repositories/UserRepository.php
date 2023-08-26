<?php

namespace App\Models\Databases\Repositories;

use App\Models\Entities\UserEntity;

/**
 * USERSテーブルを操作するリポジトリクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
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
     * @return string|false 登録したユーザーID
     * @throws PDOException
     */
    public function insert(UserEntity $user): string|false
    {
        $sql = 'INSERT INTO ' . $this->tableName . '(user_name, email, password) VALUES(:user_name, :email, :password)';
        $parameters = [
          ':user_name' => $user->getUserName(),
          ':email' => $user->getEmail(),
          ':password' => $user->getPassword()
        ];

        $this->dbConnection->executeQuery($sql, $parameters);
        $this->logger->info($sql);

        return $this->dbConnection->lastInsertId();
    }

    /**
     * ユーザーIDによるユーザー取得
     *
     * @param string $userId
     * @return UserEntity
     */
    public function fetchUserById(string $userId): UserEntity
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE id=:id AND status=' . UserEntity::ACTIVE;

        $parameters = [':id' => $userId];
        $record = $this->dbConnection->fetchFirstResult($sql, $parameters);
        $this->logger->info($sql);

        if (count($record) === 0) {
            return null;
        }

        return UserEntity::toEntity($record);

    }

    /**
     * ユーザー名に一致するユーザーを取得する
     *
     * @param string $name
     * @return UserEntity|false ユーザーが見つからなければ、falseを返す
     */
    public function fetchUserByName(string $name): UserEntity|false
    {
        $sql = 'SELECT id, user_name, email, password, status, login_at, created_at, updated_at FROM ' . $this->tableName . ' WHERE user_name=:user_name AND status=' . UserEntity::ACTIVE;

        $parameters = [':user_name' => $name];

        $record = $this->dbConnection->fetchFirstResult($sql, $parameters);
        $this->logger->info($sql);

        if (! $record) {
            return false;
        }

        return UserEntity::toEntity($record);
    }
}
