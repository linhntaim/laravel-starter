<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Middleware\Api;

use App\Http\Middleware\Client as BaseClientApp;
use App\Http\Requests\Request;

class Client extends BaseClientApp
{
    protected function setClient(Request $request)
    {
        return parent::setClient($request)
            ->decryptHeaders($request)
            ->setClientFromRequestHeader($request);
    }
}
