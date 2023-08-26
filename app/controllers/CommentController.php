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

/**
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class CommentController extends AbstractController
{
    private CommentService $commentService;
    private BoardService $boardService;
    private Authentication $auth;

    /**
     * 初期化等を行う
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);

        $this->commentService = new CommentService(new CommentRepository(new DBConnection()));
        $this->boardService = new BoardService(new BoardRepository(new DBConnection()));
        $this->auth = new Authentication($this->session);

        // バリデーションルール
        $this->validatorRules = [
            'comment' => [
                'name' => 'コメント',
                'rules' => ['required','lengthMax:1000']
            ]
        ];
    }

    /**
     * コメント投稿に関する操作を行う
     * 投稿成功後リダイレクトを行う
     *
     * @param integer $boardId
     * @return void
     */
    public function post(int $boardId): void
    {
        $parameters = $this->request->getAllParameters();
        $errorMsgs = $this->validate($parameters);

        // csrfエラーメッセージ追加
        $errorMsgs = [...$errorMsgs ,...$this->csrfVerify($parameters['token'])];

        // エラーメッセージ付きで、ビューを表示
        if (count($errorMsgs) > 0) {
            $this->index($boardId, $parameters, $errorMsgs);
        }

        try {
            // コメント登録処理
            $userId = (new SessionManager())->get(getAppConfig('sessionAuthKey'));
            $this->commentService->post($parameters['comment'], $userId, $boardId);

            $this->response->redirect("/board/{$boardId}");
        } catch(\PDOException $e) {
            $this->logger->error("コメント登録に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    /**
     * 掲示板IDに基づいてコメント一覧をビューに渡す処理を行う
     *
     * @param integer $boardId
     * @param array $originValues 投稿失敗時の値
     * @param array $errorMsgs 投稿失敗時のエラーメッセージ
     * @return void
     */
    public function index(int $boardId, array $originValues = [], array $errorMsgs = []): void
    {
        try {
            // スレッド情報取得
            $board = $this->boardService->fetchBoard($boardId);

            // スレッドが存在しなければ、エラー画面
            if (is_null($board)) {
                $this->response->redirect('/error/404');
            }

            // ビューで利用する変数
            $comments = $this->commentService->fetchComments($boardId);
            $csrfToken = $this->csrfHandler->create();

            require_once __DIR__ . '/../views/pages/board.php';
            exit;
        } catch (\PDOException $e) {
            $this->logger->error("コメント取得に失敗しました: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    /**
     * コメント削除に関する処理をする
     * 成功時、元のコメント一覧画面にリダイレクトする
     *
     * @param integer $commentId
     * @return void
     */
    public function delete(int $commentId): void
    {
        try {
            $comment = $this->commentService->fetchComment($commentId);
            $createdUserId = $comment->getUserId();

            // 権限検証
            if(! $this->auth->isAuthenticatedUser($createdUserId)) {
                $userId = $this->session->get('user_id');

                // エラー処理
                $this->logger->info("ユーザーID: {$userId}はcommentId: {$commentId}のリソースを削除できません");
                $this->response->redirect('/error/403');
            }

            // コメント削除
            $this->commentService->delete($comment);

            $boardId = $comment->getBoardId();
            $this->response->redirect("/board/{$boardId}");
        } catch (\PDOException $e) {
            $this->logger->error("コメント削除に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }
}
