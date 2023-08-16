<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Config\RouteAuthStatus;
use App\Controllers\AuthenticationController;
use App\Controllers\UserController;
use App\Middlewares\Request;
use App\Middlewares\Response;
use App\Router\Router;

$request = new Request();
$response = new Response();
$router = new Router(
    $request,
    $response,
    ['post', '/sign_up', [UserController::class, 'add', RouteAuthStatus::UnAuthenticated]],
    ['get', '/sign_up', [UserController::class, 'viewSignup',  RouteAuthStatus::UnAuthenticated]],
    ['get', '/sign_in', [AuthenticationController::class, 'viewSignin', RouteAuthStatus::UnAuthenticated]],
    ['post', '/sign_in', [AuthenticationController::class, 'authenticate', RouteAuthStatus::UnAuthenticated]],
);

$router->resolve();
