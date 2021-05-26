<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Events\AdminPasswordResetAutomaticallyEvent;
use App\Http\Controllers\Api\Auth\PasswordController as BasePasswordController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\Utils\ConfigHelper;

/**
 * Class PasswordController
 * @package App\Http\Controllers\Api\Admin\Auth
 * @property AdminRepository $userRepository
 */
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

    protected function isAutomatic()
    {
        return ConfigHelper::get('forgot_password_enabled.admin_auto');
    }

    public function store(Request $request)
    {
        if (!ConfigHelper::get('forgot_password_enabled.admin')) {
            $this->abort404();
        }
        return parent::store($request);
    }

    protected function getPasswordResetAutomaticallyEventClass()
    {
        return AdminPasswordResetAutomaticallyEvent::class;
    }
}
