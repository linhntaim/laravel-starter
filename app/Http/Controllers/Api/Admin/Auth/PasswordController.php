<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\PasswordController as BasePasswordController;
use App\Http\Requests\Request;
use App\Models\Admin;
use App\Utils\ConfigHelper;
use Illuminate\Support\Facades\Password;

class PasswordController extends BasePasswordController
{
    protected function broker()
    {
        return Password::broker('admins');
    }

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

    protected function resetValidatedRules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string', sprintf('min:%d', Admin::MIN_PASSWORD_LENGTH), 'confirmed'],
        ];
    }
}
