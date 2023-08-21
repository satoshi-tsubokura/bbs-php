<?php

namespace App\Models\Databases\Repositories;

use App\Models\Entities\BoardEntity;

/**
 * BOARDSテーブルを操作するリポジトリクラス
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
            $createdAt = new \DateTime($record['created_at']);
            $updatedAt = new \DateTime($record['updated_at']);
            // TODO FETCHモードをFETCH_OBJに変えることで対策できないか検討
            $boards[] = new BoardEntity($record['id'], $record['user_id'], $record['title'], $record['description'], $record['status'], $createdAt, $updatedAt);
        }

        return $boards;
    }

    public function countAllBoards(): int
    {
        $countCol = 'boardNum';
        $sql = 'SELECT COUNT(*) AS ' . $countCol . ' FROM ' . $this->tableName  . ' WHERE status=' . BoardEntity::ACTIVE;

        $result = $this->dbConnection->fetchFirstResult($sql);
        $this->logger->info($sql);
        return $result[$countCol];

    }
}
