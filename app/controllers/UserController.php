<?php

namespace App\Controllers;

use App\Kernels\AbstractController;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Models\Databases\DBConnection;
use App\Models\Databases\Repositories\UserRepository;
use App\Services\AuthenticateService;
use App\Services\UserService;

class UserController extends AbstractController
{
    private UserService $userService;
    private AuthenticateService $authService;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $userRepo = new UserRepository(new DBConnection());
        $this->userService = new UserService($userRepo);
        $this->authService = new AuthenticateService($userRepo);

        $this->validatorRules = [
            'name' => [
                'name' => 'ユーザー名',
                'rules' => ['required','lengthMin:3','lengthMax:20']
            ],
            'email' => [
                'name' => 'メールアドレス',
                'rules' => ['required', 'email']
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
     * ユーザー登録に関する処理を行う
     *
     * @return void
     */
    public function signup()
    {
        $parameters = $this->request->getAllParameters();
        $errorMsgs = $this->validate($parameters);

        try {
            if (count($errorMsgs) === 0) {
                // ユーザー登録処理
                $user = $this->userService->registerUser($parameters['name'], $parameters['email'], $parameters['password']);
                $errorMsgs = $user ? [] : ['messages' => ['ユーザー名もしくはメールアドレスが既に存在しています。']];
            }

            // エラーメッセージがあった場合
            $this->logger->info('$errorMsgs: ', $errorMsgs);
            if (count($errorMsgs) > 0) {
                $this->viewSignUp($parameters, $errorMsgs);
            } else {
                // 新規登録成功
                $this->authService->authenticate($user);

                // 掲示板一覧画面へリダイレクト
                $this->response->redirect('/');
            }
        } catch(\PDOException $e) {
            $this->logger->error("ユーザー登録に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    public function viewSignUp(array $parameters = [], array $errorMsgs = []): void
    {
        require_once __DIR__ . '/../views/pages/sign_up.php';
    }
}
