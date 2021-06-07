<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Events\AdminPasswordResetAutomaticallyEvent;
use App\Events\AdminPasswordResetEvent;
use App\Http\Controllers\Api\Auth\PasswordController as BasePasswordController;
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

    protected function enabled()
    {
        return ConfigHelper::get('forgot_password_enabled.admin');
    }

    protected function automated()
    {
        return ConfigHelper::get('forgot_password_enabled.admin_auto');
    }

    protected function getPasswordResetEventClass()
    {
        return AdminPasswordResetEvent::class;
    }

    protected function getPasswordResetAutomaticallyEventClass()
    {
        return AdminPasswordResetAutomaticallyEvent::class;
    }
}
