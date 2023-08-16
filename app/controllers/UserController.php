<?php

namespace App\Controllers;

use App\Middlewares\Validations\RequestValidator;
use App\Middlewares\Request;
use App\Middlewares\Response;
use App\Models\Databases\Repositories\UserRepository;
use App\Services\AuthenticateService;
use App\Services\UserService;
use App\Utils\SessionManager;

class UserController extends AbstractController
{
    private const SIGN_UP_VIEW_PATH = __DIR__ . '/../views/pages/sign_up.php';

    private array $validatorRules;
    private UserService $userService;
    private AuthenticateService $authService;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $userRepo = new UserRepository();
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
    public function add()
    {
        // 認証済みの場合、リダイレクトする

        // バリデーション処理
        $parameters = $this->request->getAllParameters();
        $validator = new RequestValidator($this->validatorRules, $parameters);
        $errorMsgs = $validator->validate();

        try {
            if (count($errorMsgs) === 0) {
                // ユーザー登録処理
                $user = $this->userService->registerUser($parameters['name'], $parameters['email'], $parameters['password']);
                $errorMsgs = $user ? [] : ['messages' => ['ユーザー名もしくはメールアドレスが既に存在しています。']];
            }

            // エラーメッセージがあった場合
            $this->logger->info('$errorMsgs: ', $errorMsgs);
            if (count($errorMsgs) > 0) {
                require_once self::SIGN_UP_VIEW_PATH;
            } else {
                // 新規登録成功
                $this->authService->authenticate($user);
                // TODO: 掲示板一覧画面へ
                // header('Location: /');
            }
        } catch(\PDOException $e) {
            $this->logger->error("ユーザー登録に失敗: {$e->getMessage()}", $e->getTrace());

            // TODO: エラー画面
            // header('Location: /error');
        }
    }

    public function viewSignUp()
    {
        require_once self::SIGN_UP_VIEW_PATH;
    }
}
