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

class BoardController extends AbstractController
{
    private BoardService $boardService;
    private SessionManager $session;
    private CsrfHandler $csrfHandler;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->boardService = new BoardService(new BoardRepository(new DBConnection()));
        $this->session = new SessionManager();
        $this->csrfHandler = new CsrfHandler();

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

    public function create()
    {
        // バリデーション処理
        $parameters = $this->request->getAllParameters();
        $errorMsgs = $this->validate($parameters);

        // csrf検証
        if (! $this->csrfHandler->verify($parameters['token'])) {
            $errorMsgs = ['messages' => ['不正なアクセスを確認いたしました。']];
        }

        try {
            // バリデーションエラーがなければ、登録処理を行う
            if (count($errorMsgs) === 0) {
                $this->session->start();
                $userId = $this->session->get(getAppConfig('sessionAuthKey'));
                $errorMsgs = $this->boardService->create($parameters['title'], $parameters['description'], $userId);
            }

            if (count($errorMsgs) > 0) {
                $this->viewCreate($parameters, $errorMsgs);
                return;
            }

            $this->response->redirect('/');
        } catch (\PDOException $e) {
            $this->logger->error("スレッド登録に失敗: {$e->getMessage()}", $e->getTrace());

            // TODO: エラー画面
            $this->response->redirect("/error");
        }
    }

    public function viewCreate(array $originValues = [], array $errorMsgs = []): void
    {
        $csrfToken = $this->csrfHandler->create();
        require_once __DIR__ . '/../views/pages/board_create.php';
    }

    public function index()
    {
        $currentPage = (int) ($this->request->getAllParameters()['page'] ?? 1);
        $maxBoardsNum = getAppConfig('maxBoardsNum');
        $boards = $this->boardService->fetchBoards($currentPage, $maxBoardsNum);

        // ページネーション処理
        $allBoardsNum = $this->boardService->countAllBoards();
        $maxPage = (int) ceil($allBoardsNum / $maxBoardsNum);

        require_once __DIR__ . '/../views/pages/index.php';
    }

    public function listComments(int $boardId): void
    {
        $csrfToken = $this->csrfHandler->create();
        require_once __DIR__ . '/../views/pages/board.php';
    }
}
