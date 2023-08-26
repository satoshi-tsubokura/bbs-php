<?php

namespace App\Models\Entities;

/**
 * エンティティ変換処理を定義したインターフェース
 * 
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
interface IToEntity
{
    /**
     * レコード1行分の結果セット配列をエンティティクラスに変換する
     *
     * @param array $record
     * @param string $colPrefix
     * @return void
     */
    public static function toEntity(array $record, string $colPrefix = ''): self;
}
