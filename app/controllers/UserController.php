<?php

namespace App\Controllers;

use App\Middlewares\Request;

class UserController extends AbstractController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index()
    {
        print 'success';
    }
}
