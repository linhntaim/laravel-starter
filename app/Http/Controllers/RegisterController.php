<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Utils\SocialLogin;

abstract class RegisterController extends ModelApiController
{
    public function store(Request $request)
    {
        if (SocialLogin::getInstance()->enabled()) {
            if ($request->has('_social')) {
                return $this->storeSocial($request);
            }
        }
        return $this->responseFail();
    }

    public abstract function storeSocial(Request $request);
}
