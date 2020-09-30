<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

abstract class ApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->withInlineMiddleware();
    }
}
