<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\ModelApiController;
use App\Http\Requests\Request;
use App\ModelRepositories\AdminRepository;
use App\Exceptions\AppException;
use App\ModelResources\AdminAccountResource;

class AdminAccountController extends ModelApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new AdminRepository();
    }

    public function index(Request $request)
    {
        $model = $this->modelRepository->notStrict()->getById($request->user()->id);
        if (empty($model)) {
            throw new AppException(static::__transErrorWithModule('not_found'));
        }
        return $this->responseModel(
            $this->setModelResourceClass(AdminAccountResource::class)->modelTransform($model)
        );
    }
}
