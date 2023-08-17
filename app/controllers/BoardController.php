<?php

namespace App\Controllers;

use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\Securities\CsrfHandler;
use App\Models\Databases\Repositories\BoardRepository;
use App\Services\BoardService;
use App\Kernels\SessionManager;

class BoardController extends AbstractController
{
    private const CREATE_VIEW_PATH = __DIR__ . '/../views/pages/board_create.php';
    private BoardService $boardService;
    private SessionManager $session;
    private CsrfHandler $csrfMiddleware;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->boardService = new BoardService(new BoardRepository());
        $this->session = new SessionManager();
        $this->csrfMiddleware = new CsrfHandler();

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
        if (! $this->csrfMiddleware->verify($parameters['token'])) {
            $errorMsgs = ['messages' => ['不正なアクセスを確認いたしました。']];
        }

        try {
            // バリデーションエラーがなければ、登録処理を行う
            if (count($errorMsgs) === 0) {
                $this->session->start();
                $userId = $this->session->get('user_id');
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
            header('Location: /error');
        }
    }

    public function viewCreate(array $originValues = [], array $errorMsgs = []): void
    {
        $csrfToken = $this->csrfMiddleware->create();
        require_once self::CREATE_VIEW_PATH;
    }
}
