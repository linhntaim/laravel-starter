<?php

namespace App\Utils\Theme;

use App\Http\Requests\Request;

abstract class ViewShare
{
    protected function shared(Request $request)
    {
        return [];
    }

    public function share(Request $request)
    {
        $view = view();
        foreach ($this->shared($request) as $key => $shared) {
            $view->share($key, $shared);
        }
    }
}
