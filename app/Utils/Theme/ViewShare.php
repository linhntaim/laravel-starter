<?php

namespace App\Utils\Theme;

use App\Http\Requests\Request;

abstract class ViewShare
{
    public abstract function share(Request $request);
}
