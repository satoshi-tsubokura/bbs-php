<?php

namespace App\Controllers;

use App\Kernels\AbstractController;
use App\Models\Databases\Repositories\UserRepository;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\SessionManager;
use App\Models\Databases\DBConnection;
use App\Services\AuthenticateService;

/**
 * 認証に関するコントローラークラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class AuthenticationController extends AbstractController
{
    private AuthenticateService $authService;

    /**
     * @override
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->authService = new AuthenticateService(new UserRepository(new DBConnection()));

        // バリデーションルール
        $this->validatorRules = [
          'name' => [
            'name' => 'ユーザー名',
            'rules' => ['required','lengthMin:3','lengthMax:20']
          ],
          'password' => [
            'name' => 'パスワード',
            'rules' => [
              'required',
              'lengthMin:10',
              'lengthMax:72',
              'regex:/\A(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9_]+\z/'
            ],
            'messages' => [
              'regex' => '{field} は英大文字、英小文字、数字を必ず含んでください。また、{field} は英数字と_のみで構成してください。'
            ]
          ],
        ];

    }

    /**
     * バリデーション、認証処理を行う
     *
     * @return void
     */
    public function signin()
    {
        // パラメーター値事態に対するバリデーション
        $parameters = $this->request->getAllParameters();
        $errorMsgs = $this->validate($parameters);

        try {
            if (count($errorMsgs) === 0) {
                $user = $this->authService->fetchAuthUser($parameters['name'], $parameters['password']);

                if (! $user) {
                    // 認証に失敗した場合
                    $errorMsgs = [
                      'messages' => ['入力したユーザー名もしくはパスワードに一致するユーザーが見つかりませんでした。']
                    ];
                }
            }

            // エラーメッセージがあれば画面表示
            if(count($errorMsgs) > 0) {
                $this->viewSignin($parameters, $errorMsgs);
                exit;
            }

            // 該当するユーザーが存在した場合
            $this->authService->authenticate($user);
            $this->response->redirect('/');
        } catch(\PDOException $e) {
            $this->error("ユーザー認証処理に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    /**
     * サインアウト処理
     *
     * @return void
     */
    public function signout(): void
    {
        $this->session->destroy();

        $this->response->redirect('/');
    }

    /**
     * 認証画面の表示
     *
     * @param array $originValues 投稿失敗時の値
     * @param array $errorMsgs 投稿失敗時のエラーメッセージ
     * @return void
     */
    public function viewSignin(array $originValues = [], array $errorMsgs = []): void
    {
        require_once __DIR__ . '/../views/pages/sign_in.php';
        ;
    }
}
