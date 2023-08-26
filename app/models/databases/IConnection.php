<?php

namespace App\Models\Databases;

/**
 * データリソースに接続するためのインターフェース
 */
interface IConnection
{
    public function connect(): void;
    public function close(): void;
}
