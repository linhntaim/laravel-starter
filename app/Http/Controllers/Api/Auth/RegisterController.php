<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\Utils\SocialLogin;

abstract class RegisterController extends ModelApiController
{
    public function store(Request $request)
    {
        if (SocialLogin::getInstance()->enabled()) {
            if ($request->has('_social')) {
                return $this->registerSocially($request);
            }
        }
        return $this->register($request);
    }

    public function register(Request $request)
    {
        return $this->responseFail();
    }

    public function registerSocially(Request $request)
    {
        return $this->responseFail();
    }
}
