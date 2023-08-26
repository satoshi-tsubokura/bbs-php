<?php

namespace App\Models\Databases\Repositories;

use App\Models\Databases\DBConnection;
use App\Kernels\AppLogger;

/**
 * リポジトリクラス共通の処理を定義した抽象クラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
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
