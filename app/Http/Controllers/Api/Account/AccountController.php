<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Account;

use App\Http\Requests\Request;
use App\ModelRepositories\UserRepository;
use App\ModelResources\UserAccountResource;

class AccountController extends BaseAccountController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new UserRepository();
        $this->setFixedModelResourceClass(
            UserAccountResource::class,
            $this->modelRepository->modelClass()
        );
    }

    public function store(Request $request)
    {
        $this->modelRepository->model($this->getAccountModel($request));

        // TODO:

        // TODO

        return parent::store($request);
    }
}
