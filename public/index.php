<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Kernels\Configs\RouteAuthStatus;
use App\Controllers\AuthenticationController;
use App\Controllers\BoardController;
use App\Controllers\CommentController;
use App\Controllers\UserController;
use App\Kernels\Http\Request;
use App\Kernels\Http\Response;
use App\Kernels\Router;

$request = new Request();
$response = new Response();
$router = new Router(
    $request,
    $response,
    ['post', '/sign_up', [UserController::class, 'signup', RouteAuthStatus::UnAuthenticated]],
    ['get', '/sign_up', [UserController::class, 'viewSignup',  RouteAuthStatus::UnAuthenticated]],
    ['get', '/sign_in', [AuthenticationController::class, 'viewSignin', RouteAuthStatus::UnAuthenticated]],
    ['post', '/sign_in', [AuthenticationController::class, 'signin', RouteAuthStatus::UnAuthenticated]],
    ['post', '/sign_out', [AuthenticationController::class, 'signout', RouteAuthStatus::Required]],
    ['get', '/create/board', [BoardController::class, 'viewCreate', RouteAuthStatus::Required]],
    ['post', '/create/board', [BoardController::class, 'create', RouteAuthStatus::Required]],
    ['get', '/', [BoardController::class, 'index', RouteAuthStatus::Optional]],
    ['get', '/board/{boardId:\d+}', [CommentController::class, 'index', RouteAuthStatus::Optional]],
    ['post', '/board/{boardId:\d+}', [CommentController::class, 'post', RouteAuthStatus::Required]],
);

$router->resolve();
