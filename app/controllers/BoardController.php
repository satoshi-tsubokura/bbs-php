<?php

namespace App\Controllers;

use App\Kernels\AbstractController;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\Securities\CsrfHandler;
use App\Models\Databases\Repositories\BoardRepository;
use App\Services\BoardService;
use App\Kernels\SessionManager;
use App\Models\Databases\DBConnection;

use function App\Kernels\Utils\getAppConfig;

/**
 * 掲示板に関するコントローラークラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class BoardController extends AbstractController
{
    private BoardService $boardService;

    /**
     * @override
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->boardService = new BoardService(new BoardRepository(new DBConnection()));
        $this->session = new SessionManager();

        // バリデーションルール
        $this->validatorRules = [
          'title' => [
            'name' => 'タイトル',
            'rules' => ['required', 'lengthMax:50']
          ],
          'description' => [
            'name' => '詳細',
            'rules' => ['lengthMax:1000']
          ]
        ];
    }

    /**
     * バリデーション・掲示板作成処理
     *
     * @return void
     */
    public function create(): void
    {
        // バリデーション処理
        $parameters = $this->request->getAllParameters();
        $errorMsgs = $this->validate($parameters);

        // csrfエラーメッセージ追加
        $errorMsgs = [...$errorMsgs ,...$this->csrfVerify($parameters['token'])];

        try {
            // バリデーションエラーがなければ、登録処理を行う
            if (count($errorMsgs) === 0) {
                $this->session->start();
                $userId = $this->session->get(getAppConfig('sessionAuthKey'));
                $errorMsgs = $this->boardService->create($parameters['title'], $parameters['description'], $userId);
            }

            if (count($errorMsgs) > 0) {
                $this->viewCreate($parameters, $errorMsgs);
                exit;
            }

            $this->response->redirect('/');
        } catch (\PDOException $e) {
            $this->logger->error("スレッド登録に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    /**
     * 掲示板作成画面表示、表示前の処理
     *
     * @param array $originValues 投稿失敗時の値
     * @param array $errorMsgs 投稿失敗時のエラーメッセージ
     * @return void
     */
    public function viewCreate(array $originValues = [], array $errorMsgs = []): void
    {
        $csrfToken = $this->csrfHandler->create();
        require_once __DIR__ . '/../views/pages/board_create.php';
    }

    /**
     * 掲示板一覧表示、表示前の処理
     *
     * @return void
     */
    public function index(): void
    {
        $currentPage = (int) ($this->request->getAllParameters()['page'] ?? 1);
        $maxBoardsNum = getAppConfig('maxBoardsNum');
        $boards = $this->boardService->fetchBoards($currentPage, $maxBoardsNum);

        // ページネーション処理
        $allBoardsNum = $this->boardService->countAllBoards();
        $maxPage = (int) ceil($allBoardsNum / $maxBoardsNum);

        require_once __DIR__ . '/../views/pages/index.php';
    }
}
