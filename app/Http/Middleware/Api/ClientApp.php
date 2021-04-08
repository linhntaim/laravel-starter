<?php

namespace App\Http\Middleware\Api;

use App\Http\Middleware\ClientApp as BaseClientApp;
use App\Http\Requests\Request;

class ClientApp extends BaseClientApp
{
    protected function setClient(Request $request)
    {
        return parent::setClient($request)
            ->setClientAppFromRequestHeader($request)
            ->decryptHeaders($request);
    }
}