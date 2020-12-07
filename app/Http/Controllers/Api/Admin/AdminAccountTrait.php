<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Request;

trait AdminAccountTrait
{
    protected function getAccountModel(Request $request)
    {
        return $request->admin();
    }
}
