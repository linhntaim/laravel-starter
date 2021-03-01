<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\WebController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends WebController
{
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->afterLogout();
    }

    protected function afterLogout()
    {
        return redirect('/');
    }
}
