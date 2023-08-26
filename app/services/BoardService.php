<?php

namespace App\Services;

use App\Models\Databases\Repositories\BoardRepository;
use App\Models\Entities\BoardEntity;

/**
 * 掲示板に関するサービスクラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class BoardService
{
    /**
     * @param BoardRepository $boardRepository
     */
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

    /**
     * 総掲示板数を取得する
     *
     * @return integer 総掲示板数
     */
    public function countAllBoards(): int
    {
        return $this->boardRepository->countAllBoards();
    }

    /**
     * 掲示板をIDによって取得する
     *
     * @param integer $boardId
     * @return BoardEntity|null 掲示板エンティティ
     */
    public function fetchBoard(int $boardId): BoardEntity|null
    {
        return $this->boardRepository->fetchById($boardId);
    }
}
