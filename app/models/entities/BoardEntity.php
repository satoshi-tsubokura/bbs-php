<?php

namespace App\Models\Entities;

/**
 * BOARDSテーブルに対応したエンティティクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class BoardEntity
{
    public const ACTIVE = 0;
    public const ARCHIVED = 1;

    public function __construct(
        private ?int $id,
        private int $userId,
        private string $title,
        private ?string $description = '',
        private int $status = self::ACTIVE,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
