<?php

namespace App\Services;

use App\Models\Databases\Repositories\BoardRepository;
use App\Models\Entities\BoardEntity;
use App\Kernels\SessionManager;

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

    /**
     * 指定したページのスレッド一覧を取得する
     *
     * @param integer $page
     * @param integer $max 最大スレッド数
     * @return array BoardEntityインスタンスを格納した配列
     * @throws \PDOException
     */
    public function fetchBoards(int $page, int $max): array
    {
        $offset = ($page - 1) * $max;
        return $this->boardRepository->fetchBoards($max, $offset) ?? [];
    }

    public function countAllBoards(): int
    {
        return $this->boardRepository->countAllBoards();
    }

    public function fetchBoard(int $boardId): BoardEntity
    {
        return $this->boardRepository->fetchById($boardId);
    }
}
