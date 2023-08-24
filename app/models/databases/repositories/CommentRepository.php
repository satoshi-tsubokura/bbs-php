<?php

namespace App\Models\Databases\Repositories;

use App\Models\Entities\CommentEntity;
use App\Models\Entities\UserEntity;

/**
 * BOARDSテーブルを操作するリポジトリクラス
 */
class CommentRepository extends AbstractMysqlRepository
{
    /**
     * @override
     * @return void
     */
    protected function init(): void
    {
        $this->tableName = 'COMMENTS';
    }

    public function insert(string $commentBody, int $userId, int $boardId): string|false
    {
        try {
            $this->dbConnection->transaction();

            $commentNo = $this->fetchNextCommentNo($boardId);

            // コメント登録
            $sql = 'INSERT INTO ' . $this->tableName . '(user_id, board_id, comment_no, comment_body) VALUES(:user_id, :board_id, :comment_no, :comment_body)';

            $parameters = [
                ':user_id' => $userId,
                ':board_id' => $boardId,
                ':comment_no' => $commentNo,
                ':comment_body' => $commentBody
            ];

            $this->dbConnection->executeQuery($sql, $parameters);
            $this->logger->info($sql);

            $lastInsertId = $this->dbConnection->lastInsertId();

            // BOARD更新日の更新
            $this->updatedAtBoard($boardId);

            $this->dbConnection->commit();

            return $lastInsertId;
        } catch (\PDOException $e) {
            $this->dbConnection->rollback();
            throw $e;
        }
    }

    private function fetchNextCommentNo(int $boardId)
    {
        $maxCol = 'maxCommentNo';
        $sql = 'SELECT MAX(comment_no) AS ' . $maxCol . ' FROM ' . $this->tableName . ' WHERE board_id=:board_id';

        $parameters = [':board_id' => $boardId];

        $record = $this->dbConnection->fetchFirstResult($sql, $parameters);
        $this->logger->info($sql);

        return $record[$maxCol] + 1;
    }

    public function fetchAllByBoardId(int $boardId): array
    {
        // statusカラムの値関係なく取得する
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE board_id=:board_id';

        $parameters = [':board_id' => $boardId];
        $records = $this->dbConnection->fetchResultsAll($sql, $parameters);


        foreach ($records as $record) {
            // ユーザー情報取得
            $user = $this->fetchUser($record['user_id']);

            // CommentEntityに変換
            $comments[] = $this->ToEntity($record, $user);
        }

        return $comments ?? [];
    }

    private function ToEntity(array $record, UserEntity $user = null): CommentEntity
    {
        // 型変換
        $createdAt = new \DateTime($record['created_at']);
        $updatedAt = new \DateTime($record['updated_at']);

        return new CommentEntity($record['id'], $record['user_id'], $record['board_id'], $record['comment_no'], $record['comment_body'], $record['status'], $createdAt, $updatedAt, $user);
    }

    private function updatedAtBoard(int $boardId): bool
    {
        $boardRepository = new BoardRepository($this->dbConnection);
        return $boardRepository->updatedAt($boardId);
    }

    private function fetchUser(int $userId): UserEntity
    {
        $userRepository = new UserRepository($this->dbConnection);
        return $userRepository->fetchUserById($userId);
    }
}
