<?php

namespace App\Controllers;

use App\Kernels\AbstractController;
use App\Models\Databases\Repositories\UserRepository;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\SessionManager;
use App\Models\Databases\DBConnection;
use App\Services\AuthenticateService;

class AuthenticationController extends AbstractController
{
    private const SIGN_IN_VIEW_PATH = __DIR__ . '/../views/pages/sign_in.php';
    private AuthenticateService $authService;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->authService = new AuthenticateService(new UserRepository(new DBConnection()));

        $this->validatorRules = [
          'name' => [
            'name' => 'ユーザー名',
            //                                   Note: マルチバイト文字が先頭にあるケースで正確に判定してくれないため、一時的な対策
            'rules' => ['required','lengthMin:3','lengthMax:21']
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

    public function signin()
    {
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
                require_once self::SIGN_IN_VIEW_PATH;
                return;
            }

            // 該当するユーザーが存在した場合
            $this->authService->authenticate($user);
            $this->response->redirect('/');
        } catch(\PDOException $e) {
            $this->error("ユーザー認証処理に失敗: {$e->getMessage()}", $e->getTrace());

            $this->response->redirect('/error');
        }
    }

    public function signout(): void
    {
        $session = new SessionManager();
        $session->destroy();

        $this->response->redirect('/');
    }

    public function viewSignin(array $parameters = [], array $errorMsgs = []): void
    {
        require_once self::SIGN_IN_VIEW_PATH;
    }
}
