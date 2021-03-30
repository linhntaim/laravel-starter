<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

class WebController extends Controller
{
    public function __construct()
    {
        $this->setValidationThrown(false);
    }

    protected function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        $this->transactionComplete();
        return redirect($to, $status, $headers, $secure);
    }

    protected function redirectRoute($route, $parameters = [], $status = 302, $headers = [])
    {
        $this->transactionComplete();
        return redirect()->route($route, $parameters, $status, $headers);
    }

    protected function view($view = 'index', $data = [], $mergeData = [])
    {
        $this->transactionComplete();
        return view($view, $data, $mergeData);
    }
}
