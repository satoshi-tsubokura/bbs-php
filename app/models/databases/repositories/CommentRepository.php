<?php

namespace App\Models\Databases\Repositories;

use App\Models\Entities\CommentEntity;
use App\Models\Entities\UserEntity;

/**
* COMMENTSテーブルを操作するリポジトリクラス
*
* @author satoshi tsubokura <tsubokurajob151718@gmail.com>
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

    /**
     * Note: インサート成功時、BOARDSテーブルのupdated_atを更新する
     *
     * @param string $commentBody
     * @param integer $userId
     * @param integer $boardId
     * @return string|false 成功時、インサートに成功したレコードのIDを返す。失敗時、falseを返す
     * @throws \PDOException
     */
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

    /**
     * 指定した掲示板IDのコメントをstatusに関わらず、すべて取得する。
     *
     * @param integer $boardId
     * @return array<CommentEntity> コメント一覧
     */
    public function fetchAllByBoardId(int $boardId): array
    {
        $userPrefix = 'u_';
        $userColmuns = '
            u.id AS ' . $userPrefix . 'id,
            u.user_name AS ' . $userPrefix . 'user_name,
            u.email AS ' . $userPrefix . 'email,
            u.password AS ' . $userPrefix . 'password,
            u.status AS ' . $userPrefix . 'status,
            u.login_at AS ' . $userPrefix . 'login_at,
            u.created_at AS ' . $userPrefix . 'created_at,
            u.updated_at AS ' . $userPrefix . 'updated_at';

        // statusカラムの値関係なく取得する
        $sql = 'SELECT c.*, ' . $userColmuns . ' FROM ' . $this->tableName . ' AS c JOIN USERS AS u on c.user_id=u.id WHERE c.board_id=:board_id ORDER BY c.comment_no ASC';

        $parameters = [':board_id' => $boardId];
        $records = $this->dbConnection->fetchResultsAll($sql, $parameters);

        foreach ($records as $record) {
            // レコード->エンティティ変換処理
            $user = UserEntity::toEntity($record, $userPrefix);
            $comment = CommentEntity::ToEntity($record);
            $comment->setUser($user);

            $comments[] = $comment;
        }

        return $comments ?? [];
    }

    /**
     * 指定したコメントIDのコメントをstatusに関わらず、取得する。
     *
     * @param integer $id
     * @return CommentEntity
     */
    public function fetchById(int $id): CommentEntity
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE id=:id';

        $parameters = [':id' => $id];

        $record = $this->dbConnection->fetchFirstResult($sql, $parameters);

        if (! $record) {
            return null;
        }

        return CommentEntity::toEntity($record);
    }

    /**
     * statusを変更する
     *
     * @param integer $id
     * @param integer $status CommentEntityの定数のみ許可する
     * @return boolean 変更が成功したか
     */
    public function changeStatus(int $id, int $status): bool
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET status=:status WHERE id=:id';

        $parameters = [
            ':status' => $status,
            ':id' => $id
        ];

        return $this->dbConnection->executeQuery($sql, $parameters);
    }

    /**
     * 指定した掲示板の最大のコメント番号+1の値を返す
     *
     * @param integer $boardId
     * @return integer 最大コメント番号 + 1
     */
    private function fetchNextCommentNo(int $boardId): int
    {
        $maxCol = 'maxCommentNo';
        $sql = 'SELECT MAX(comment_no) AS ' . $maxCol . ' FROM ' . $this->tableName . ' WHERE board_id=:board_id';

        $parameters = [':board_id' => $boardId];

        $record = $this->dbConnection->fetchFirstResult($sql, $parameters);
        $this->logger->info($sql);

        return $record[$maxCol] + 1;
    }

    /**
     * BOARDSテーブルの更新日を更新する
     *
     * @param integer $boardId
     * @return boolean 更新が成功したか
     */
    private function updatedAtBoard(int $boardId): bool
    {
        $boardRepository = new BoardRepository($this->dbConnection);
        return $boardRepository->updatedAt($boardId);
    }
}
