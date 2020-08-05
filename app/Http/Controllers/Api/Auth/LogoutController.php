<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;

class LogoutController extends ApiController
{
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->responseSuccess();
    }
}
