<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Api\PasswordController as BasePasswordController;
use App\Http\Requests\Request;
use App\Utils\ConfigHelper;

class PasswordController extends BasePasswordController
{
    public function index(Request $request)
    {
        if (ConfigHelper::get('forgot_password_enabled.admin')) {
            return parent::index($request);
        }
        return $this->abort404();
    }

    public function store(Request $request)
    {
        if (ConfigHelper::get('forgot_password_enabled.admin')) {
            return parent::store($request);
        }
        return $this->abort404();
    }
}
