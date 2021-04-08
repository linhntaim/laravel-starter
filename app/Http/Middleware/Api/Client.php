<?php

namespace App\Http\Middleware\Api;

use App\Http\Middleware\Client as BaseClientApp;
use App\Http\Requests\Request;

class Client extends BaseClientApp
{
    protected function setClient(Request $request)
    {
        return parent::setClient($request)
            ->setClientFromRequestHeader($request)
            ->decryptHeaders($request);
    }
}
