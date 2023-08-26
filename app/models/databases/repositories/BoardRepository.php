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
            $boards[] = BoardEntity::toEntity($record);
        }

        return $boards;
    }

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

    public function countAllBoards(): int
    {
        $countCol = 'boardNum';
        $sql = 'SELECT COUNT(*) AS ' . $countCol . ' FROM ' . $this->tableName  . ' WHERE status=' . BoardEntity::ACTIVE;

        $result = $this->dbConnection->fetchFirstResult($sql);
        $this->logger->info($sql);
        return $result[$countCol];
    }

    public function updatedAt(int $id): bool
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET updated_at=CURRENT_TIMESTAMP() WHERE id=:id';

        $parameters = [':id' => $id];

        $this->logger->info($sql);
        return $this->dbConnection->executeQuery($sql, $parameters);
    }
}
