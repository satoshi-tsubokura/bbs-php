<?php

namespace App\Controllers;

use App\Models\Databases\Repositories\UserRepository;
use App\Middlewares\Request;
use App\Middlewares\Response;
use App\Middlewares\Validations\RequestValidator;
use App\Services\AuthenticateService;
use App\Services\UserService;
use App\Utils\AppLogger;
use App\Utils\AuthUtil;

class AuthenticationController extends AbstractController
{
    private const SIGN_IN_VIEW_PATH = __DIR__ . '/../views/pages/sign_in.php';

    private array $validatorRules;
    private AuthenticateService $authService;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->authService = new AuthenticateService(new UserRepository());

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
        $validator = new RequestValidator($this->validatorRules, $parameters);
        $errorMsgs = $validator->validate();

        if(count($errorMsgs) > 0) {
            require_once self::SIGN_IN_VIEW_PATH;
            exit();
        }

        try {
            $user = $this->authService->fetchAuthUser($parameters['name'], $parameters['password']);

            if ($user) {
                // 該当するユーザーが存在した場合
                $this->authService->authenticate($user);
                $this->response->redirect('/');
            } else {
                // 認証に失敗した場合
                $errorMsgs = [
                  'messages' => ['入力したユーザー名もしくはパスワードに一致するユーザーが見つかりませんでした。']
                ];

                // ログイン画面表示
                require_once self::SIGN_IN_VIEW_PATH;
            }

        } catch(\PDOException $e) {
            $this->error("ユーザー認証処理に失敗: {$e->getMessage()}", $e->getTrace());

            // エラー画面表示
            $this->response->redirect('/error');
        }
    }

    public function signout()
    {
        $this->authService->unAuthenticate();
        $this->response->redirect('/');
    }

    public function viewSignin()
    {
        require_once self::SIGN_IN_VIEW_PATH;
    }
}
