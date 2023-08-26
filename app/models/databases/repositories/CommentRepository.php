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
        $userColmuns = 'u.id AS u_id, u.user_name AS u_user_name, u.email AS u_email, u.password AS u_password, u.status AS u_status, u.login_at AS u_login_at, u.created_at AS u_created_at, u.updated_at AS u_updated_at';
        // statusカラムの値関係なく取得する
        $sql = 'SELECT c.*, ' . $userColmuns . ' FROM ' . $this->tableName . ' AS c JOIN USERS AS u on c.user_id=u.id WHERE c.board_id=:board_id ORDER BY c.comment_no ASC';

        $parameters = [':board_id' => $boardId];
        $records = $this->dbConnection->fetchResultsAll($sql, $parameters);

        foreach ($records as $record) {
            $comments[] = $this->ToEntity($record);
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

        return $this->ToEntity($record);
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
     * レコード1行分の結果セットをエンティティに変更する
     *
     * @param array $record
     * @param UserEntity|null $user コメントを投稿したユーザーエンティティ
     * @return CommentEntity
     */
    private function ToEntity(array $record): CommentEntity
    {
        // UserEntityに変換
        $UserLoginAt = new \DateTime($record['u_login_at']);
        $UserCreatedAt = new \DateTime($record['u_created_at']);
        $UserUpdatedAt = new \DateTime($record['u_updated_at']);
        $user = new UserEntity($record['u_id'], $record['u_user_name'], $record['u_email'], $record['u_password'], $record['u_status'], $UserLoginAt, $UserCreatedAt, $UserUpdatedAt);

        // CommentEntityに変換
        $CommentCreatedAt = new \DateTime($record['created_at']);
        $CommentUpdatedAt = new \DateTime($record['updated_at']);
        return new CommentEntity($record['id'], $record['user_id'], $record['board_id'], $record['comment_no'], $record['comment_body'], $record['status'], $CommentCreatedAt, $CommentUpdatedAt, $user);
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

    /**
     * USERSレコード一行に対応するエンティティを取得する
     *
     * @param integer $userId
     * @return UserEntity
     */
    private function fetchUser(int $userId): UserEntity
    {
        $userRepository = new UserRepository($this->dbConnection);
        return $userRepository->fetchUserById($userId);
    }
}
