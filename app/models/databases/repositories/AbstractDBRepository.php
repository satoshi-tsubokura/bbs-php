<?php

namespace App\Models\Databases\Repositories;

use App\Models\Databases\DBConnection;

abstract class AbstractDBRepository
{
    protected string $tableName;
    protected DBConnection $dbConnection;

    /**
     * 初期化メソッド
     *
     * @return void
     */
    abstract protected function init(): void;
}
