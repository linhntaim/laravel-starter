<?php

namespace App\Utils\Theme;

use App\Http\Requests\Request;

class AuthViewShare extends ViewShare
{
    public function share(Request $request)
    {
        $user = $request->user();
        $auth = !!$user;
        $view = view();
        $view->share('auth', $auth);
        $view->share('user', $user);
    }
}
