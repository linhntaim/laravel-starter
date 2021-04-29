<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\WebController;
use App\Http\Requests\Request;

class IndexController extends WebController
{
    use WelcomeTrait;

    public function index(Request $request, $path = null)
    {
        return $this->welcome($request, $path);
    }
}
