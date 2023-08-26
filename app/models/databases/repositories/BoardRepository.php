<?php

namespace App\Models\Databases\Repositories;

use App\Models\Entities\BoardEntity;

/**
 * BOARDSテーブルを操作するリポジトリクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class BoardRepository extends AbstractMysqlRepository
{
    /**
     * @override
     * @return void
     */
    protected function init(): void
    {
        $this->tableName = 'BOARDS';
    }

    /**
     * 同じタイトルの掲示板の存在チェックをする
     *
     * @param string $title
     * @return boolean 同じタイトルが存在しているか
     * @throws \PDOException
     */
    public function existsSameTitle(string $title): bool
    {
        $countCol = 'count';
        $sql = 'SELECT COUNT(*) AS ' . $countCol . ' FROM ' . $this->tableName  . ' WHERE title=:title AND status=' . BoardEntity::ACTIVE;

        $parameters = ['title' => $title];

        $result = $this->dbConnection->fetchFirstResult($sql, $parameters);
        $this->logger->info($sql);

        return $result[$countCol] !== 0;
    }

    /**
     * BOARDSテーブルに新規レコードを追加する処理
     *
     * @param BoardEntity $board
     * @return string|false lastInsertId
     * @throws \PDOException
     */
    public function insert(BoardEntity $board): string|false
    {
        $sql = 'INSERT INTO ' . $this->tableName . '(user_id, title, description) VALUES(:user_id, :title, :description)';

        $parameters = [
          ':user_id' => $board->getUserId(),
          ':title' => $board->getTitle(),
          ':description' => $board->getDescription()
        ];

        $this->dbConnection->executeQuery($sql, $parameters);
        $this->logger->info($sql);

        return $this->dbConnection->lastInsertId();
    }

    /**
     * レコードを指定区間取得するメソッド
     *
     * @param integer $limit 最大取得数
     * @param integer $offset 取得開始インデックス
     * @return array<BoardEntity>
     * @throws \PDOException
     */
    public function fetchBoards(int $limit, int $offset): array
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE status=' . BoardEntity::ACTIVE . ' ORDER BY updated_at desc LIMIT :limit OFFSET :offset';

        $parameters = [
          ':limit' => $limit,
          ':offset' => $offset
        ];

        $records = $this->dbConnection->fetchResultsAll($sql, $parameters);
        $this->logger->info($sql);

        // BoardEntityに変換
        foreach ($records as $record) {
            $boards[] = BoardEntity::toEntity($record);
        }

        return $boards;
    }

    /**
     * 掲示板IDによって1レコードに対応するBoardEntityを取得する。
     *
     * @param integer $id
     * @return BoardEntity|null
     * @throws \PDOException
     */
    public function fetchById(int $id): BoardEntity|null
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE id=:id AND status=' . BoardEntity::ACTIVE;

        $parameters = [':id' => $id];

        $record = $this->dbConnection->fetchFirstResult($sql, $parameters);

        if (! $record) {
            return null;
        }

        return BoardEntity::toEntity($record);
    }

    /**
     * 有効なステータスの掲示板数を取得する
     *
     * @return integer
     * @throws \PDOException
     */
    public function countAllBoards(): int
    {
        $countCol = 'boardNum';
        $sql = 'SELECT COUNT(*) AS ' . $countCol . ' FROM ' . $this->tableName  . ' WHERE status=' . BoardEntity::ACTIVE;

        $result = $this->dbConnection->fetchFirstResult($sql);
        $this->logger->info($sql);
        return $result[$countCol];
    }

    /**
     * 指定したIDの更新日時を現在時刻に更新する
     *
     * @param integer $id
     * @return boolean 更新に成功したか
     * @throws \PDOException
     */
    public function updatedAt(int $id): bool
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET updated_at=CURRENT_TIMESTAMP() WHERE id=:id';

        $parameters = [':id' => $id];

        $this->logger->info($sql);
        return $this->dbConnection->executeQuery($sql, $parameters);
    }
}
