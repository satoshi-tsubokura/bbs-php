<?php

namespace App\Models\Databases\Repositories;

use App\Models\Databases\DBConnection;
use App\Kernels\AppLogger;

abstract class AbstractDBRepository
{
    protected string $tableName;
    protected DBConnection $dbConnection;
    protected AppLogger $logger;

    /**
     * 初期化メソッド
     *
     * @return void
     */
    abstract protected function init(): void;
}
