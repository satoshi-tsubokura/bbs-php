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

    public function fetchUserById(string $userId): UserEntity
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE id=:id AND status=0';

        $parameters = [':id' => $userId];
        $record = $this->dbConnection->fetchFirstResult($sql, $parameters);
        $this->logger->info($sql);

        if (count($record) === 0) {
            return null;
        }

        $loginAt = new \DateTime($record['login_at']);
        $createdAt = new \DateTime($record['created_at']);
        $updatedAt = new \DateTime($record['updated_at']);

        return new UserEntity($record['id'], $record['user_name'], $record['email'], $record['password'], $record['status'], $loginAt, $createdAt, $updatedAt);

    }
}
