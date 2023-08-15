<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Middlewares\Request;
use App\Router\Router;

require_once '../app/config/app_config.php';

$request = new Request();
$router = new Router(
    $request,
    ['post', '/sign_up', [UserController::class, 'add']],
    ['get', '/sign_up', [UserController::class, 'viewSignup']],
);

$router->resolve();
