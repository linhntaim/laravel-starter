<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Request;
use App\Models\ActivityLog;

abstract class LogoutController extends ApiController
{
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $this->logAction(ActivityLog::ACTION_LOGOUT);
        return $this->responseSuccess();
    }
}
