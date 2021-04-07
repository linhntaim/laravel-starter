<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers;

use App\Utils\Theme\ThemeResponseTrait;

class WebController extends Controller
{
    use ThemeResponseTrait;

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

    protected function view($view = 'index', $data = [], $mergeData = [], $withTheme = true)
    {
        $this->transactionComplete();
        return $withTheme ? $this->themeView($view, $data, $mergeData) : view($view, $data, $mergeData);
    }

    protected function viewHome($data = [], $mergeData = [], $view = 'welcome')
    {
        $this->transactionComplete();
        return view($view, $data, $mergeData);
    }
}
