<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Request;

trait AdminAccountTrait
{
    protected function getAccountModel(Request $request)
    {
        return $request->admin();
    }
}
