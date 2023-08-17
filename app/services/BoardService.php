<?php

namespace App\Services;

use App\Models\Databases\Repositories\BoardRepository;
use App\Models\Entities\BoardEntity;
use App\Utils\SessionManager;

class BoardService
{
    public function __construct(
        private BoardRepository $boardRepository
    ) {
    }

    /**
     * 同じタイトルのスレッドが存在しているかチェックする
     *
     * @param string $title
     * @return void
     * @throws \PDOException
     */
    public function existsSameTitle(string $title)
    {
        return $this->boardRepository->existsSameTitle($title);
    }

    /**
     * 掲示板スレッドを登録するための処理
     *
     * @param string $title
     * @param string $description
     * @return array エラーメッセージ
     * @throws \PDOException
     */
    public function create(string $title, string $description = '', int $userId): array
    {
        // 同じタイトルのスレッドが存在すれば、エラーメッセージを返す
        if ($this->existsSameTitle($title)) {
            $errorMsgs = ['title' => ['同じタイトルのスレッドが既に存在しているため、作成できません。']];
        } else {
            $boardEntity = new BoardEntity(id: null, userId: $userId, title: $title, description: $description);
            $this->boardRepository->insert($boardEntity);
        }

        return $errorMsgs ?? [];
    }
}
