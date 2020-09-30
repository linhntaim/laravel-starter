<?php

/**
 * Base - Any modification needs to be approved, except the space inside the block of TODO
 */

namespace App\Http\Controllers\Api\Account;

use App\Exceptions\AppException;
use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\UserRepository;
use App\ModelResources\UserAccountResource;

class AccountController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new UserRepository();
    }

    public function index(Request $request)
    {
        $model = $this->modelRepository->notStrict()->getById($request->user()->id);
        if (empty($model)) {
            throw new AppException(static::__transErrorWithModule('not_found'));
        }
        return $this->responseModel(
            $this->setModelResourceClass(UserAccountResource::class)->modelTransform($model)
        );
    }
}
