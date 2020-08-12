<?php

namespace App\Http\Controllers;

abstract class ApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->withInlineMiddleware();
    }
}
