<?php

namespace App\Services;

use App\Models\Databases\Repositories\BoardRepository;
use App\Models\Databases\Repositories\CommentRepository;
use App\Models\Entities\CommentEntity;
use PhpParser\Comment;

class CommentService
{
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

    public function fetchComments(int $boardId): array
    {
        return $this->commentRepository->fetchAllByBoardId($boardId);
    }

    public function fetchComment(int $commentId): CommentEntity|null
    {
        return $this->commentRepository->fetchById($commentId);
    }

    public function delete(int $commentId): bool
    {
        return $this->commentRepository->changeStatus($commentId, CommentEntity::ARCHIVED);
    }
}
