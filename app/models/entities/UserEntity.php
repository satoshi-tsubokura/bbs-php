<?php

namespace App\Models\Entities;

/**
 * USERSテーブルに対応したエンティティクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class UserEntity implements IToEntity
{
    public const ACTIVE = 0;
    public const NOT_ACTIVE = 1;

    public function __construct(
        private ?int $id,
        private string $userName,
        private string $email,
        private string $password,
        private int $status = self::ACTIVE,
        private ?\DateTime $loginAt = null,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null,
    ) {
    }

    /**
     * @override
     *
     * @param array $record
     * @param string $colPrefix as句でつけたプレフィックス
     * @return UserEntity
     */
    public static function toEntity(array $record, string $colPrefix = ''): UserEntity
    {
        $createdAt = new \DateTime($record[$colPrefix . 'created_at']);
        $updatedAt = new \DateTime($record[$colPrefix . 'updated_at']);
        $loginAt = $record[$colPrefix . 'login_at'] ? new \DateTime(record[$colPrefix . 'login_at']) : null;
        return new UserEntity(
            $record[$colPrefix . 'id'],
            $record[$colPrefix . 'user_name'],
            $record[$colPrefix . 'email'],
            $record[$colPrefix . 'password'],
            $record[$colPrefix . 'status'],
            $loginAt,
            $createdAt,
            $updatedAt
        );
    }

    /*********************************************************
    * ゲッター
    ***********************************************************/

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return integer
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return \DateTime|null
     */
    public function getLoginAt(): ?\DateTime
    {
        return $this->loginAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->loginAt;
    }
}
