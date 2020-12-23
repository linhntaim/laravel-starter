<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Account;

use App\Http\Requests\Request;
use App\ModelRepositories\UserRepository;
use App\ModelResources\UserAccountResource;

/**
 * Class UserAccountController
 * @package App\Http\Controllers\Api\Account
 */
abstract class UserAccountController extends AccountController
{
    protected function getAccountRepositoryClass()
    {
        return UserRepository::class;
    }

    protected function getAccountResourceClass()
    {
        return UserAccountResource::class;
    }

    public function store(Request $request)
    {
        $this->modelRepository->model($this->getAccountModel($request));

        // TODO:

        // TODO

        return parent::store($request);
    }

    // TODO:

    // TODO
}
