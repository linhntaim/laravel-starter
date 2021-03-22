<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\WebController;
use App\Http\Requests\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;

class LogoutController extends WebController
{
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->afterLogout($request);
    }

    protected function afterLogout(Request $request)
    {
        return $this->redirect(RouteServiceProvider::HOME);
    }
}
