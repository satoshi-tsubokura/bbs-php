<?php

namespace App\Kernels;

use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\Validator;
use App\Kernels\AppLogger;
use App\Kernels\Securities\CsrfHandler;

/**
 * コントローラー共通処理を定義した抽象クラス
 *
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
abstract class AbstractController
{
    protected AppLogger $logger;
    protected array $validatorRules;
    protected SessionManager $session;
    protected CsrfHandler $csrfHandler;

    /**
     * コントローラー共通で利用するプロパティの設定を行う
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(
        protected Request $request,
        protected Response $response
    ) {
        $this->logger = AppLogger::getInstance();
        $this->session = new SessionManager();
        $this->csrfHandler = new CsrfHandler($this->session);
    }

    /**
     * リクエストパラメーターのバリデーション結果を返す
     *
     * @return array エラーメッセージ
     */
    protected function validate(array $parameters): array
    {
        $validator = new Validator($this->validatorRules, $parameters);
        return $validator->validate();
    }

    /**
     * csrf検証をして、エラーメッセージを行う。
     *
     * @param string $token
     * @return array エラーメッセージ
     */
    protected function csrfVerify(string $token): array
    {
        $errorMsgs = [];

        if (! $this->csrfHandler->verify($token)) {
            $errorMsgs = ['messages' => ['不正なアクセスを確認いたしました。']];
        }

        return $errorMsgs;
    }
}
