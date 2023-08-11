<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Middlewares\Request;
use App\Router\Router;

$request = new Request();
$router = new Router(
    $request,
    // ['get', '/user', [UserController::class, 'index']],
);

$router->resolve();
