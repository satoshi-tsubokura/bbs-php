<?php

namespace App\Models\Entities;

/**
 * BOARDSテーブルに対応したエンティティクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class CommentEntity
{
    public const ACTIVE = 0;
    public const ARCHIVED = 1;

    public function __construct(
        private ?int $id,
        private int $userId,
        private int $boardId,
        private int $commentNo,
        private string $commentBody,
        private int $status = self::ACTIVE,
        private ?\DateTime $createdAt = null,
        private ?\DateTime $updatedAt = null,
        // USERSテーブルリレーション
        private ?UserEntity $user = null
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

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function getCommentNo(): int
    {
        return $this->commentNo;
    }

    public function getCommentBody(): string
    {
        return $this->commentBody;
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

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }
}
