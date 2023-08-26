<?php

namespace App\Services;

use App\Models\Databases\Repositories\CommentRepository;
use App\Models\Entities\CommentEntity;

/**
 * 掲示板コメントに関するビジネスロジックを実装するクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class CommentService
{
    /**
     * @param CommentRepository $commentRepository
     */
    public function __construct(
        private CommentRepository $commentRepository
    ) {
    }

    /**
     * コメント投稿に関連する処理を行う
     *
     * @param string $comment
     * @param integer $userId
     * @param integer $boardId
     * @return bool 投稿が成功したか否か
     * @throws \PDOException
     */
    public function post(string $comment, int $userId, int $boardId): bool
    {
        return $this->commentRepository->insert($comment, $userId, $boardId);
    }

    /**
     * 指定した掲示板のコメント一覧を取得する
     *
     * @param integer $boardId
     * @return array<CommentEntity>
     */
    public function fetchComments(int $boardId): array
    {
        return $this->commentRepository->fetchAllByBoardId($boardId);
    }

    /**
     * コメントIDによってコメントを取得する
     *
     * @param integer $commentId
     * @return CommentEntity|null
     */
    public function fetchComment(int $commentId): CommentEntity|null
    {
        return $this->commentRepository->fetchById($commentId);
    }

    /**
     * コメントを論理削除する
     *
     * @param CommentEntity $comment
     * @return boolean コメントが削除されているか
     */
    public function delete(CommentEntity $comment): bool
    {
        if ($comment->getStatus() !== CommentEntity::ARCHIVED) {
            return $this->commentRepository->changeStatus($comment->getId(), CommentEntity::ARCHIVED);
        }

        return true;
    }
}
