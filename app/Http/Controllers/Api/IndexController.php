<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;

class IndexController extends ApiController
{
    public function index(Request $request)
    {
        $this->abort404();
    }
}