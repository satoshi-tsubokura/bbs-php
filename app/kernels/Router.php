<?php

namespace App\Kernels;

use App\Kernels\Configs\RouteAuthStatus;
use App\Kernels\AbstractController;
use App\Exceptions\InvalidTypeException;
use App\Kernels\Auth\Authentication;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use FastRoute\Dispatcher;

/**
 * ルーティングを行うクラス
 * @author satoshi tsubokura <tsubokurajob151718@gmail.com>
 */
class Router
{
    /**
     * http_method, path_info, handlerをキーに持つ配列を格納する配列
     * @var array
     */
    private array $routeDataList = [];
    private Request $request;
    private Response $response;
    public const ALLOW_HTTP_METHOD = ['get', 'post', 'delete', 'put'];

    /**
     * ルート情報を設定する
     *
     * @param Request $request
     * @param array{
     *  [
     *    0: string HTTPメソッド
     *    1: string URIパターン
     *    2: array|Closure コントローラメソッド|クロージャー
     *  ]
     * } $routeDataList 複数のルートデータ
     */
    public function __construct(Request $request, Response $response, array ...$routeDataList)
    {
        $this->request = $request;
        $this->response = $response;
        array_walk($routeDataList, function ($data) {
            $this->registerRouteData($data);
        });
    }

    /**
     * ルート情報を登録する
     *
     * @param array{
     *  0: string HTTPメソッド
     *  1: string URIパターン
     *  2: array|Closure コントローラメソッド｜クロージャー
     * } $routeData
     * @return void
     * @throws InvalidArgumentsException
     */
    public function registerRouteData(array $routeData): void
    {
        if (! $this->isAllowedMethod($routeData[0])) {
            throw new \InvalidArgumentException('許可していないHTTPメソッドを登録しています。');
        }

        if (! $this->isValidHandler($routeData[2])) {
            throw new \InvalidArgumentException('不正なハンドラーが登録されています。');
        }

        $routeData[0] = strtolower($routeData[0]);
        $this->routeDataList[] = $routeData;
    }

    /**
     * ルーティングを実際に実施するメソッド
     *
     * @return void
     */
    public function resolve(): void
    {
        // リクエストヘッダの内容を取得
        $httpMethod = $this->request->getRequestMethod();
        $uriPath = $this->request->getPath();
        $routeInfo = $this->dispatch($httpMethod, $uriPath);

        // TODO: ハンドラー($routeInfo[1])の型がClosureである場合の処理を追加する
        if (isset($routeInfo[1]) && is_array($routeInfo[1])) {
            // 認証状態によるリダイレクト処理
            $routeAuth = $routeInfo[1][2] ?? RouteAuthStatus::Optional;
            $auth = new Authentication();
            $auth->handleRoute(new Response(), $routeAuth);
        }

        $this->runDispatchFunc($routeInfo, $httpMethod);
    }

    /**
     * 許可しているHTTPメソッドを判定
     *
     * @param string $httpMethod
     * @return boolean
     */
    private function isAllowedMethod(string $httpMethod): bool
    {
        return in_array(strtolower($httpMethod), self::ALLOW_HTTP_METHOD);
    }

    /**
     * ハンドラーの形式チェック
     *
     * @param mixed $handler
     * @return boolean
     */
    private function isValidHandler(mixed $handler): bool
    {
        if ($handler instanceof \Closure) {
            return true;
        }

        if (is_array($handler) && count($handler) >= 2) {
            return true;
        }

        return false;
    }

    /**
     * 事前登録したルーティング情報を割り当てる
     *
     * @param string $httpMethod
     * @param string $uriPath
     * @return array ルーティング結果を格納した配列
     */
    private function dispatch(string $httpMethod, string $uriPath): array
    {
        // 登録したルーティング情報をディスパッチャに追加
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            array_walk(
                $this->routeDataList,
                fn ($data) => $r->addRoute(...$data)
            );
        });

        return $dispatcher->dispatch($httpMethod, $uriPath);
    }

    /**
     * 実際にコントローラクラスのメソッドや関数(クロージャー)を実行する
     *
     * @param array $routeInfo
     * @return void
     */
    private function runDispatchFunc(array $routeInfo, string $httpMethod): void
    {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // TODO: 404エラーページの表示
                print 'NOT FOUND';
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                // TODO: 405エラーページの表示
                print 'METHOD NOT ALLOWED';
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                // クラスメソッドが割り当てられている場合
                if (is_array($handler)) {
                    $controller = new $handler[0]($this->request, $this->response);

                    if (! $controller instanceof AbstractController) {
                        throw new InvalidTypeException('AbstractControllerを継承しいないクラスを実行できません。');
                    }

                    // Closureに変換
                    $handler = [$controller, $handler[1]](...$vars);
                    break;
                }

                $handler(...$vars);
                break;
            default:
                // TODO: 500エラーレスポンスの表示
                print 'INTERNAL SERVER ERROR';
        }
    }
}
