<?php

namespace App\Controllers;

use App\Middlewares\Request;

abstract class AbstractController
{
    public function __construct(
        protected Request $request
    ) {
    }
}
