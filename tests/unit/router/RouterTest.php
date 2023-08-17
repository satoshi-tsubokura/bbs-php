<?php

namespace Tests\Unit\Router;

use App\Kernels\AbstractController;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernelsr\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\CustomTestCase;

final class RouterTest extends CustomTestCase
{
    private Router $router;
    private Request $requestStub;
    private Response $responseStub;

    public function setUp(): void
    {
        $this->requestStub = $this->createStub(Request::class);
        $this->responseStub  = $this->createStub(Response::class);
        $this->router = new Router($this->requestStub, $this->responseStub);
    }

    #[DataProvider('validRouteDataListProvider')]
    public function testSuccessRegisterRoute(array $routeDataList): void
    {
        // ルーティング情報の登録
        foreach($routeDataList as $routeData) {
            $this->router->registerRouteData($routeData);
        }

        $routeDataList = $this->getPrivateProperty($this->router, 'routeDataList');
        $this->assertCount(3, $routeDataList);
    }

    public function testRegisterInvalidHttpMethod(): void
    {
        $invalidMethodRoute = ['head', '/test', function () {}];

        $this->expectException(\InvalidArgumentException::class);
        $this->router->registerRouteData($invalidMethodRoute);
    }

    public function testRegisterInvalidMethodHandler(): void
    {
        $controller = new class ($this->requestStub, $this->responseStub) extends AbstractController {
            public function index()
            {
                print 'testController';
            }
        };
        // コントローラークラスしか登録していない場合
        $invalidMethodRoute = ['get', '/test', [$controller::class]];

        $this->expectException(\InvalidArgumentException::class);
        $this->router->registerRouteData($invalidMethodRoute);
    }

    public function testRegisterInvalidTypeHandler(): void
    {
        // コントローラークラスしか登録していない場合
        $invalidMethodRoute = ['get', '/test', 'string'];

        $this->expectException(\InvalidArgumentException::class);
        $this->router->registerRouteData($invalidMethodRoute);
    }

    // ルーティング成功ケース
    #[DataProvider('validRouteDataListProvider')]
    public function testSuccessResolve(array $routeDataList, array $httpMethod, mixed $expected): void
    {
        // リクエストヘッダーのスタブの設定
        $this->requestStub->method('getRequestMethod')->willReturn(strtolower($httpMethod['method_name']));
        $this->requestStub->method('getPath')->willReturn(strtolower($httpMethod['uri']));

        // ルートデータの登録
        foreach($routeDataList as $routeData) {
            $this->router->registerRouteData($routeData);
        }

        // 標準出力の内容を取得
        ob_start();
        $this->router->resolve();
        $actual = ob_get_clean();

        $this->assertEquals($expected, $actual);
    }

    public function testErrorResolve(): void
    {
        $this->markTestSkipped('エラー処理を未実装のため、スキップします。');
    }

    /**
     * @return array{
     *  array(
     *    http_method: string,
     *    path_info: string,
     *    handler: array | Closure,
     *  )
     * }
     */
    public static function validRouteDataListProvider(): array
    {
        $controller = new class (new Request(), new Response()) extends AbstractController {
            public function index(int $id)
            {
                print "id: {$id}";
            }
        };

        $routeDataList =  [
          ['post', '/test/{id:\d+}', [$controller::class, 'index']],
          ['GET',  '/test', function () {
              print 'Closure';
          }],
          ['delete', '/test/delete/{user_id:\d+}/{board_id:\d+}', function ($user_id, $board_id) {
              print "deleted board_id: {$board_id}, user_id: {$user_id}";
          }]
        ];

        $httpMethodList = [
          ['method_name' => 'POST', 'uri' => '/test/1'],
          ['method_name' => 'GET', 'uri' => '/test'],
          ['method_name' => 'DELETE', 'uri' => '/test/delete/100/1'],
        ];

        $expectedList = [
          'id: 1',
          'Closure',
          'deleted board_id: 1, user_id: 100'
        ];

        return array_map(fn ($hm, $a) => [$routeDataList, $hm, $a], $httpMethodList, $expectedList);
    }
}
