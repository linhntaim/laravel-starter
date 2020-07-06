<?php

namespace App\Http\Controllers;

class ApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->withInlineMiddleware();
    }
}
