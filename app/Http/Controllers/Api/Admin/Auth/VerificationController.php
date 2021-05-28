<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Api\Auth\VerificationController as BaseVerificationController;
use App\ModelRepositories\AdminRepository;

class VerificationController extends BaseVerificationController
{
    protected function modelRepositoryClass()
    {
        return AdminRepository::class;
    }

    // TODO:

    // TODO
}