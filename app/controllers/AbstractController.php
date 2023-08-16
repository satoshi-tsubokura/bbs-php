<?php

namespace App\Controllers;

use App\Middlewares\Request;
use App\Middlewares\Response;
use App\Utils\AppLogger;

abstract class AbstractController
{
    protected AppLogger $logger;
    public function __construct(
        protected Request $request,
        protected Response $response
    ) {
        $this->logger = AppLogger::getInstance();
    }
}
