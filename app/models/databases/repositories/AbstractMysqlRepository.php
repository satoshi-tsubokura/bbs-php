<?php

namespace App\Models\Databases\Repositories;

use App\Models\Databases\DBConnection;
use App\Kernels\AppLogger;

/**
 * Mysqlのデータベース処理の共通化
 */
abstract class AbstractMysqlRepository extends AbstractDBRepository
{
    /**
     * @param IConnection $dbConnection データベース接続クラス
     */
    public function __construct(DBConnection $connection)
    {
        $this->dbConnection = $connection;
        $this->logger = AppLogger::getInstance();
        $this->init();
    }
}
