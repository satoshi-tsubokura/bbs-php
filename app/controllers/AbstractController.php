<?php

namespace App\Controllers;

use App\Middlewares\Request;
use App\Utils\AppLogger;

abstract class AbstractController
{
    protected AppLogger $logger;
    public function __construct(
        protected Request $request
    ) {
        $this->logger = AppLogger::getInstance();
    }
}
