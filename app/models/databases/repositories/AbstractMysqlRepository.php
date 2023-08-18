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
    public function __construct()
    {
        $this->dbConnection = new DBConnection();
        $this->dbConnection->connect();
        $this->logger = AppLogger::getInstance();
        $this->init();
    }
}
