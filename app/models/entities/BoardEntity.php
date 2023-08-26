<?php

namespace App\Models\Entities;

/**
 * BOARDSテーブルに対応したエンティティクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class BoardEntity implements IEntity
{
    public const ACTIVE = 0;
    public const ARCHIVED = 1;

    // テーブルリレーション
    private ?UserEntity $user = null;

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

    /**
     * @override
     *
     * @param array $record
     * @param string $colPrefix as句でつけたプレフィックス
     * @return BoardEntity
     */
    public static function toEntity(array $record, string $colPrefix = ''): BoardEntity
    {
        $createdAt = new \DateTime($record[$colPrefix . 'created_at']);
        $updatedAt = new \DateTime($record[$colPrefix . 'updated_at']);
        return new BoardEntity(
            $record[$colPrefix . 'id'],
            $record[$colPrefix . 'user_id'],
            $record[$colPrefix . 'title'],
            $record[$colPrefix . 'description'],
            $record[$colPrefix . 'status'],
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
     * @return integer
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
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
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /*********************************************************
    * セッター
    ***********************************************************/

    /**
     * @param UserEntity $user
     * @return void
     */
    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }
}
