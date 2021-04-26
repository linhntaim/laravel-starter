<?php

namespace App\Utils\Theme;

use App\Http\Requests\Request;

class AuthViewShare extends ViewShare
{
    protected function shared(Request $request)
    {
        $user = $request->user();
        return [
            'auth' => !!$user,
            'user' => $user,
        ];
    }
}
