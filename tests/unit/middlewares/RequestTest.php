<?php

namespace Tests\Unit\Middleware;

use App\Middlewares\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    private Request $request;
    public function setup(): void
    {
        $this->request = new Request();
    }

    // getRequestMethodのテスト
    public function testGetRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('get', $this->request->getRequestMethod());
    }

    // 以下、Request::getParameter()のテスト
    public function testHasNotParameterGetPath()
    {
        $_SERVER['REQUEST_URI'] = '/test/12';
        $this->assertEquals('/test/12', $this->request->getPath());
    }

    public function testHasParameterGetPath()
    {
        $_SERVER['REQUEST_URI'] = '/test?id=12';
        $this->assertEquals('/test', $this->request->getPath());
    }

    public function testIndexPathGetPath()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $this->assertEquals('/', $this->request->getPath());
    }
}
