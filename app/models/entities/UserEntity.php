<?php

namespace App\Models\Entities;

/**
 * USERSテーブルに対応したエンティティクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class UserEntity
{
    public function __construct(
        private ?int $id,
        private string $userName,
        private string $email,
        private string $password,
        private int $status = 0,
        private ?\DateTime $loginAt = null,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getLoginAt(): \DateTime
    {
        return $this->loginAt;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->loginAt;
    }
}
