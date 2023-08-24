<?php

namespace App\Models\Databases\Repositories;

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

        $record = $this->dbConnection->executeQuery($sql, $parameters);
        $this->logger->info($sql);

        return $record[$maxCol] + 1;
    }

    private function updatedAtBoard(int $boardId): bool
    {
        $boardRepository = new BoardRepository($this->dbConnection);
        return $boardRepository->updatedAt($boardId);
    }
}
