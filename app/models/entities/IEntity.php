<?php

namespace App\Models\Entities;

interface IEntity
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
