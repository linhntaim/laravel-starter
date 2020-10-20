<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Account;

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
}
