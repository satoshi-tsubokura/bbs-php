<?php

namespace App\Models\Entities;

/**
 * COMMENTSテーブルに対応したエンティティクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class CommentEntity implements IEntity
{
    // statusカラムの値を表す定数
    public const ACTIVE = 0;
    public const ARCHIVED = 1;

    // テーブルリレーション
    private ?UserEntity $user = null;
    private ?BoardEntity $board = null;

    public function __construct(
        private ?int $id,
        private int $userId,
        private int $boardId,
        private int $commentNo,
        private string $commentBody,
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
     * @return CommentEntity
     */
    public static function toEntity(array $record, string $colPrefix = ''): CommentEntity
    {
        $createdAt = new \DateTime($record[$colPrefix . 'created_at']);
        $updatedAt = new \DateTime($record[$colPrefix . 'updated_at']);
        return new CommentEntity(
            $record[$colPrefix . 'id'],
            $record[$colPrefix . 'user_id'],
            $record[$colPrefix . 'board_id'],
            $record[$colPrefix . 'comment_no'],
            $record[$colPrefix . 'comment_body'],
            $record[$colPrefix . 'status'],
            $createdAt,
            $updatedAt
        );
    }

    /*********************************************************
    * ゲッター
    ***********************************************************/

    /**
     * @return integer コメントID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return integer ユーザーID
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return integer 掲示板ID
     */
    public function getBoardId(): int
    {
        return $this->boardId;
    }

    /**
     * @return integer コメント番号
     */
    public function getCommentNo(): int
    {
        return $this->commentNo;
    }

    /**
     * @return string コメント本文
     */
    public function getCommentBody(): string
    {
        return $this->commentBody;
    }

    /**
     * @return integer ステータス定数
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return \DateTime|null 作成日
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null 更新日
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * USERSテーブルのリレーション
     *
     * @return UserEntity|null USERSテーブル
     */
    public function getUser(): ?UserEntity
    {
        return $this->user;
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

    /**
     * @param BoardEntity $board
     * @return void
     */
    public function setBoard(BoardEntity $board): void
    {
        $this->board = $board;
    }
}
