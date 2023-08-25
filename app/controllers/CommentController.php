<?php

namespace App\Controllers;

use App\Kernels\AbstractController;
use App\Kernels\Auth\Authentication;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\Securities\CsrfHandler;
use App\Kernels\SessionManager;
use App\Models\Databases\DBConnection;
use App\Models\Databases\Repositories\BoardRepository;
use App\Models\Databases\Repositories\CommentRepository;
use App\Services\BoardService;
use App\Services\CommentService;

use function App\Kernels\Utils\getAppConfig;

class CommentController extends AbstractController
{
    private CommentService $commentService;
    private BoardService $boardService;
    private CsrfHandler $csrfHandler;
    private SessionManager $session;
    private Authentication $auth;

    public function __construct(Request $request, Response $response)
    {
        $this->commentService = new CommentService(new CommentRepository(new DBConnection()));
        $this->boardService = new BoardService(new BoardRepository(new DBConnection()));
        $this->csrfHandler = new CsrfHandler();
        $this->session = new SessionManager();
        $this->auth = new Authentication($this->session);

        parent::__construct($request, $response);
        $this->validatorRules = [
            'comment' => [
                'name' => 'コメント',
                'rules' => ['required','lengthMax:1000']
            ]
        ];
    }

    public function post(int $boardId): void
    {
        $parameters = $this->request->getAllParameters();
        $errorMsgs = $this->validate($parameters);
        // csrf検証
        if (! $this->csrfHandler->verify($parameters['token'])) {
            $errorMsgs = ['messages' => ['不正なアクセスを確認いたしました。']];
        }

        // エラーメッセージ付きで、ビューを表示
        if (count($errorMsgs) > 0) {
            $this->index($boardId, $parameters, $errorMsgs);
            exit;
        }

        try {
            // コメント登録処理
            $userId = (new SessionManager())->get(getAppConfig('sessionAuthKey'));
            $this->commentService->post($parameters['comment'], $userId, $boardId);

            $this->response->redirect("/board/{$boardId}");
        } catch(\PDOException $e) {
            $this->logger->error("ユーザー登録に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    public function index(int $boardId, array $originValues = [], array $errorMsgs = []): void
    {
        try {
            // スレッド情報取得
            $board = $this->boardService->fetchBoard($boardId);

            // スレッドが存在しなければ、エラー画面
            if (is_null($board)) {
                var_dump('test');
                exit;
                $this->response->redirect('/error/404');
                exit;
            }

            // コメント取得
            $comments = $this->commentService->fetchComments($boardId);

            $csrfToken = $this->csrfHandler->create();
            require_once __DIR__ . '/../views/pages/board.php';
            exit;
        } catch (\PDOException $e) {
            $this->logger->error("コメント取得に失敗しました: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    public function delete(int $commentId): void
    {
        try {
            // 権限検証
            $comment = $this->commentService->fetchComment($commentId);
            $createdUserId = $comment->getUserId();

            if(! $this->auth->isAuthenticatedUser($createdUserId)) {
                $userId = $this->session->get('user_id');
                $this->logger->info("ユーザーID: {$userId}はcommentId: {$commentId}のリソースを削除できません");
                $this->response->redirect('/error/403');
                exit;
            }

            $this->commentService->delete($commentId);
            $boardId = $comment->getBoardId();

            $this->response->redirect("/board/{$boardId}");
        } catch (\PDOException $e) {
            $this->logger->error("コメント削除に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }
}
