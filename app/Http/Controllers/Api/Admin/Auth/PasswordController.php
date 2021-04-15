<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Api\Auth\PasswordController as BasePasswordController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\Utils\ConfigHelper;

class PasswordController extends BasePasswordController
{
    protected function getUserRepositoryClass()
    {
        return AdminRepository::class;
    }

    public function index(Request $request)
    {
        if (!ConfigHelper::get('forgot_password_enabled.admin')) {
            $this->abort404();
        }
        return parent::index($request);
    }

    public function store(Request $request)
    {
        if (!ConfigHelper::get('forgot_password_enabled.admin')) {
            $this->abort404();
        }
        return parent::store($request);
    }
}
